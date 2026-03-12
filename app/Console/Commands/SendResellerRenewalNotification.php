<?php

namespace App\Console\Commands;

use App\Mail\ResellerRenewalExpiryNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendResellerRenewalNotification extends Command
{
    protected $signature = 'reseller:send-renewal-notification';
    protected $description = 'Send weekly renewal expiry notification to resellers with licenses expiring within 90 days';

    public function handle()
    {
        $today = Carbon::now();
        $ninetyDaysFromNow = Carbon::now()->addDays(90);

        // Get all active resellers from reseller_v2
        $resellers = DB::table('reseller_v2')
            ->where('status', 'active')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        $totalSent = 0;

        foreach ($resellers as $reseller) {
            // Get linked companies for this reseller
            $resellerLinks = DB::connection('frontenddb')
                ->table('crm_reseller_link')
                ->where('reseller_id', $reseller->reseller_id)
                ->get(['f_id', 'f_company_name']);

            if ($resellerLinks->isEmpty()) {
                continue;
            }

            $companiesWithExpiry = [];

            foreach ($resellerLinks as $link) {
                // Get earliest expiring PAID Active license within 90 days
                $expiringLicense = DB::connection('frontenddb')
                    ->table('crm_company_license')
                    ->where('f_company_id', $link->f_id)
                    ->where('f_type', 'PAID')
                    ->where('status', 'Active')
                    ->where(function($q) {
                        $q->where('f_name', 'like', '%TA%')
                          ->orWhere('f_name', 'like', '%leave%')
                          ->orWhere('f_name', 'like', '%claim%')
                          ->orWhere('f_name', 'like', '%payroll%')
                          ->orWhere('f_name', 'like', '%Face & QR Code%');
                    })
                    ->whereBetween('f_expiry_date', [$today->format('Y-m-d'), $ninetyDaysFromNow->format('Y-m-d')])
                    ->orderBy('f_expiry_date', 'asc')
                    ->first(['f_expiry_date']);

                if (!$expiringLicense) {
                    continue;
                }

                // Check renewal status
                $newestLicense = DB::connection('frontenddb')
                    ->table('crm_company_license')
                    ->where('f_company_id', $link->f_id)
                    ->where('f_type', 'PAID')
                    ->where('status', 'Active')
                    ->where('f_expiry_date', '>', $expiringLicense->f_expiry_date)
                    ->where(function($q) {
                        $q->where('f_name', 'like', '%TA%')
                          ->orWhere('f_name', 'like', '%leave%')
                          ->orWhere('f_name', 'like', '%claim%')
                          ->orWhere('f_name', 'like', '%payroll%')
                          ->orWhere('f_name', 'like', '%Face & QR Code%');
                    })
                    ->orderBy('f_expiry_date', 'desc')
                    ->first(['f_expiry_date']);

                $renewalStatus = 'pending';
                if ($newestLicense) {
                    $newestExpiry = Carbon::parse($newestLicense->f_expiry_date);
                    $renewalStatus = $newestExpiry->gt($ninetyDaysFromNow) ? 'done' : 'done_expiring';
                }

                // Only include "pending" and "done_expiring" (renewed expiring soon)
                if (!in_array($renewalStatus, ['pending', 'done_expiring'])) {
                    continue;
                }

                $expiryDate = Carbon::parse($expiringLicense->f_expiry_date);

                $companiesWithExpiry[] = [
                    'company_name' => $link->f_company_name,
                    'expiry_date' => $expiringLicense->f_expiry_date,
                    'days_remaining' => $today->diffInDays($expiryDate),
                    'renewal_status' => $renewalStatus,
                ];
            }

            if (empty($companiesWithExpiry)) {
                continue;
            }

            // Sort by days remaining ascending
            usort($companiesWithExpiry, fn($a, $b) => $a['days_remaining'] - $b['days_remaining']);

            try {
                Mail::to($reseller->email)
                    ->bcc([
                        'faiz@timeteccloud.com',
                        'fatimah.tarmizi@timeteccloud.com',
                    ])
                    ->send(new ResellerRenewalExpiryNotification(
                        $reseller->company_name,
                        $companiesWithExpiry
                    ));

                $totalSent++;

                Log::info("Reseller renewal notification sent", [
                    'reseller' => $reseller->company_name,
                    'email' => $reseller->email,
                    'companies_count' => count($companiesWithExpiry),
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to send reseller renewal notification", [
                    'reseller' => $reseller->company_name,
                    'email' => $reseller->email,
                    'error' => $e->getMessage(),
                ]);

                $this->error("Failed: {$reseller->company_name} - {$e->getMessage()}");
            }
        }

        $this->info("Reseller renewal notifications sent: {$totalSent}");
        Log::info("Reseller renewal notification job completed. Total sent: {$totalSent}");
    }
}
