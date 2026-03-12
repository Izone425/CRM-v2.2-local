<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class ResellerExpiredLicenseExportController extends Controller
{
    public function export(Request $request)
    {
        $reseller = Auth::guard('reseller')->user();

        if (!$reseller || !$reseller->reseller_id) {
            abort(401, 'Unauthorized');
        }

        $activeTab = $request->get('tab', '90days');
        $today = Carbon::now();
        $ninetyDaysFromNow = Carbon::now()->addDays(90);

        // Get reseller links
        $resellerLinks = DB::connection('frontenddb')
            ->table('crm_reseller_link')
            ->where('reseller_id', $reseller->reseller_id)
            ->get(['f_id', 'f_company_name']);

        $companies = [];

        foreach ($resellerLinks as $link) {
            $query = DB::connection('frontenddb')
                ->table('crm_expiring_license')
                ->join('crm_customer', 'crm_expiring_license.f_company_id', '=', 'crm_customer.company_id')
                ->where('crm_expiring_license.f_company_id', $link->f_id)
                ->where('crm_expiring_license.f_type', 'Paid')
                ->where(function($q) use ($today) {
                    $q->whereDate('crm_expiring_license.f_expiry_date', '>=', $today->format('Y-m-d'))
                      ->orWhere('crm_customer.f_status', 'A');
                });

            if ($activeTab === '90days') {
                $query->whereDate('crm_expiring_license.f_expiry_date', '<=', $ninetyDaysFromNow->format('Y-m-d'));
            }

            $expiringLicense = $query->orderBy('crm_expiring_license.f_expiry_date', 'asc')
                ->first(['crm_expiring_license.f_expiry_date']);

            if ($expiringLicense) {
                $expiryDate = Carbon::parse($expiringLicense->f_expiry_date);
                $daysUntilExpiry = $today->diffInDays($expiryDate);

                $companies[] = [
                    'company_name' => strtoupper($link->f_company_name),
                    'expiry_date' => $expiryDate->format('Y-m-d'),
                    'days_until_expiry' => $daysUntilExpiry
                ];
            }
        }

        // Sort by expiry date ascending
        usort($companies, function($a, $b) {
            return strtotime($a['expiry_date']) - strtotime($b['expiry_date']);
        });

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(80);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);

        // Add headers
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'Company Name');
        $sheet->setCellValue('C1', 'Expiry Date');
        $sheet->setCellValue('D1', 'Days Until Expiry');

        // Style headers
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        // Add data rows
        $row = 2;
        foreach ($companies as $index => $company) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $company['company_name']);
            $sheet->setCellValue('C' . $row, $company['expiry_date']);
            $sheet->setCellValue('D' . $row, $company['days_until_expiry']);
            $row++;
        }

        // Create writer and stream response
        $writer = new Xlsx($spreadsheet);

        $filename = $activeTab === '90days'
            ? 'expired_licenses_90days_' . date('Y-m-d_His') . '.xlsx'
            : 'all_expired_licenses_' . date('Y-m-d_His') . '.xlsx';

        return new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
