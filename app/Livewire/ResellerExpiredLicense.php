<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ResellerExpiredLicense extends Component
{
    public $expandedCompany = null;
    public $invoiceDetails = [];
    public $search = '';
    public $sortField = 'days_until_expiry';
    public $sortDirection = 'asc';
    public $activeTab = '90days'; // '90days' or 'all'
    public $showRenewalModal = false;
    public $renewalDetails = [];
    public $renewalCompanyName = '';
    public $renewalStatusFilter = [];

    public function toggleRenewalStatusFilter($status)
    {
        if (in_array($status, $this->renewalStatusFilter)) {
            $this->renewalStatusFilter = array_values(array_diff($this->renewalStatusFilter, [$status]));
        } else {
            $this->renewalStatusFilter[] = $status;
        }
    }

    public function clearRenewalStatusFilter()
    {
        $this->renewalStatusFilter = [];
    }

    public function updatedSearch()
    {
        // Search updated
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->expandedCompany = null;
        $this->invoiceDetails = [];
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleExpand($fId)
    {
        // Convert to int for comparison
        $fId = (int) $fId;

        if ($this->expandedCompany === $fId) {
            $this->expandedCompany = null;
            $this->invoiceDetails = [];
        } else {
            $this->expandedCompany = $fId;
            $this->loadInvoiceDetails($fId);
        }
    }

    public function getCompaniesProperty()
    {
        $reseller = Auth::guard('reseller')->user();

        if (!$reseller || !$reseller->reseller_id) {
            return collect([]);
        }
            $today = Carbon::now();
            $ninetyDaysFromNow = Carbon::now()->addDays(90);

            // Step 1: Get f_id from crm_reseller_link where reseller_id matches
            $resellerLinks = DB::connection('frontenddb')
                ->table('crm_reseller_link')
                ->where('reseller_id', $reseller->reseller_id)
                ->get(['f_id', 'f_company_name']);

            $companies = [];

            foreach ($resellerLinks as $link) {
                // Apply search filter
                if ($this->search && stripos($link->f_company_name, $this->search) === false) {
                    continue;
                }

                // Step 2: Use f_id to get licenses from crm_company_license
                // Link crm_reseller_link.f_id with crm_company_license.f_company_id
                $query = DB::connection('frontenddb')
                    ->table('crm_company_license')
                    ->where('crm_company_license.f_company_id', $link->f_id)
                    ->where('crm_company_license.f_type', 'PAID')
                    ->where(function($q) {
                        $q->where('crm_company_license.f_name', 'like', '%TA%')
                          ->orWhere('crm_company_license.f_name', 'like', '%leave%')
                          ->orWhere('crm_company_license.f_name', 'like', '%claim%')
                          ->orWhere('crm_company_license.f_name', 'like', '%payroll%')
                          ->orWhere('crm_company_license.f_name', 'like', '%Face & QR Code%');
                    });

                // Apply filters based on active tab
                if ($this->activeTab === '90days') {
                    // Only show Active licenses expiring within 90 days from today
                    $query->where('crm_company_license.status', 'Active')
                          ->whereBetween('crm_company_license.f_expiry_date', [$today->format('Y-m-d'), $ninetyDaysFromNow->format('Y-m-d')]);
                } else {
                    // For 'all' tab, show only Active licenses with expiry >= today
                    $query->where('crm_company_license.status', 'Active')
                          ->whereDate('crm_company_license.f_expiry_date', '>=', $today->format('Y-m-d'));
                }

                $expiringLicense = $query->orderBy('crm_company_license.f_expiry_date', 'asc')
                    ->first(['crm_company_license.f_expiry_date']);

                if ($expiringLicense) {
                    $expiryDate = Carbon::parse($expiringLicense->f_expiry_date);
                    $daysUntilExpiry = $today->diffInDays($expiryDate);

                    // Check if there's a renewed license (with a later expiry date than the earliest expiring one)
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

                    // Determine renewal status: done (safe), done_expiring (renewed but still within 90 days), pending
                    $renewalStatus = 'pending';
                    if ($newestLicense) {
                        $newestExpiry = Carbon::parse($newestLicense->f_expiry_date);
                        $renewalStatus = $newestExpiry->gt($ninetyDaysFromNow) ? 'done' : 'done_expiring';
                    }

                    $companies[] = (object) [
                        'f_id' => $link->f_id,
                        'f_company_name' => $link->f_company_name,
                        'f_expiry_date' => $expiringLicense->f_expiry_date,
                        'days_until_expiry' => $daysUntilExpiry,
                        'renewal_status' => $renewalStatus,
                    ];
                }
            }

        // Filter by renewal status if selected
        if (!empty($this->renewalStatusFilter)) {
            $companies = array_filter($companies, function ($c) {
                return in_array($c->renewal_status, $this->renewalStatusFilter);
            });
            $companies = array_values($companies);
        }

        // Sort companies
        usort($companies, function($a, $b) {
            if ($this->sortField === 'f_expiry_date') {
                $comparison = strtotime($a->f_expiry_date) - strtotime($b->f_expiry_date);
            } else {
                $comparison = $a->days_until_expiry - $b->days_until_expiry;
            }

            return $this->sortDirection === 'asc' ? $comparison : -$comparison;
        });

        // Return the collection
        return collect($companies);
    }

    public function loadInvoiceDetails($fId)
    {
        $today = Carbon::now()->format('Y-m-d');
        $ninetyDaysFromNow = Carbon::now()->addDays(90)->format('Y-m-d');

        // Get reseller information
        $reseller = DB::connection('frontenddb')->table('crm_reseller_link')
            ->select('reseller_name', 'f_rate', 'f_id')
            ->where('f_id', (int) $fId)
            ->first();

        // Get all licenses for this f_id (company)
        $query = DB::connection('frontenddb')
            ->table('crm_company_license')
            ->where('crm_company_license.f_company_id', (int) $fId)
            ->where('crm_company_license.f_type', 'PAID')
            ->where(function($q) {
                $q->where('crm_company_license.f_name', 'like', '%TA%')
                  ->orWhere('crm_company_license.f_name', 'like', '%leave%')
                  ->orWhere('crm_company_license.f_name', 'like', '%claim%')
                  ->orWhere('crm_company_license.f_name', 'like', '%payroll%')
                  ->orWhere('crm_company_license.f_name', 'like', '%Face & QR Code%');
            });

        // Apply filters based on active tab
        if ($this->activeTab === '90days') {
            // Only show Active licenses that will expire within 90 days
            $query->where('crm_company_license.status', 'Active')
                  ->whereBetween('crm_company_license.f_expiry_date', [$today, $ninetyDaysFromNow]);
        } else {
            // For 'all' tab, show only Active licenses with expiry >= today
            $query->where('crm_company_license.status', 'Active')
                  ->whereDate('crm_company_license.f_expiry_date', '>=', $today);
        }

        $licenses = $query->get([
                'crm_company_license.f_name', 'crm_company_license.f_total_user',
                'crm_company_license.f_start_date',
                'crm_company_license.f_expiry_date', 'crm_company_license.f_invoice_no',
                'crm_company_license.f_billing_cycle', 'crm_company_license.status'
            ]);

        $invoiceGroups = [];
        $licenseSummary = [
            'attendance' => 0,
            'leave' => 0,
            'claim' => 0,
            'payroll' => 0
        ];

        foreach ($licenses as $license) {
            $invoiceNo = $license->f_invoice_no ?? 'No Invoice';
            $licenseName = $license->f_name;

            // Use data directly from crm_company_license table
            $quantity = $license->f_total_user;

            // Calculate module totals
            if (strpos($licenseName, 'TimeTec TA') !== false) {
                $licenseSummary['attendance'] += $quantity;
            }
            if (strpos($licenseName, 'TimeTec Leave') !== false) {
                $licenseSummary['leave'] += $quantity;
            }
            if (strpos($licenseName, 'TimeTec Claim') !== false) {
                $licenseSummary['claim'] += $quantity;
            }
            if (strpos($licenseName, 'TimeTec Payroll') !== false) {
                $licenseSummary['payroll'] += $quantity;
            }

            // Calculate amount (you may need to calculate this based on your pricing logic)
            $calculatedAmount = 0;

            // Get discount rate for display
            $discountRate = ($reseller && $reseller->f_rate) ? $reseller->f_rate : '0.00';

            if (!isset($invoiceGroups[$invoiceNo])) {
                $invoiceGroups[$invoiceNo] = [
                    'f_id' => $fId,
                    'products' => [],
                    'total_amount' => 0
                ];
            }

            $invoiceGroups[$invoiceNo]['products'][] = [
                'f_name' => $license->f_name,
                'f_total_user' => $quantity,
                'f_total_amount' => $calculatedAmount,
                'f_start_date' => $license->f_start_date,
                'f_expiry_date' => $license->f_expiry_date,
                'billing_cycle' => $license->f_billing_cycle ?? 0,
                'discount' => $discountRate,
                'status' => $license->status ?? 'Active'
            ];

            $invoiceGroups[$invoiceNo]['total_amount'] += $calculatedAmount;
        }

        $this->invoiceDetails = $invoiceGroups;
        $this->invoiceDetails['_summary'] = $licenseSummary;
    }

    public function openRenewalModal($fId)
    {
        $fId = (int) $fId;
        $today = Carbon::now();
        $ninetyDaysFromNow = Carbon::now()->addDays(90);

        // Get company name
        $resellerLink = DB::connection('frontenddb')->table('crm_reseller_link')
            ->where('f_id', $fId)
            ->first(['f_company_name', 'f_rate']);

        $this->renewalCompanyName = $resellerLink->f_company_name ?? '';

        // Find the earliest expiring license for this company (same logic as getCompaniesProperty)
        $earliestQuery = DB::connection('frontenddb')
            ->table('crm_company_license')
            ->where('f_company_id', $fId)
            ->where('f_type', 'PAID')
            ->where('status', 'Active');

        if ($this->activeTab === '90days') {
            $earliestQuery->whereBetween('f_expiry_date', [$today->format('Y-m-d'), $ninetyDaysFromNow->format('Y-m-d')]);
        } else {
            $earliestQuery->whereDate('f_expiry_date', '>=', $today->format('Y-m-d'));
        }

        $earliestLicense = $earliestQuery->orderBy('f_expiry_date', 'asc')->first(['f_expiry_date']);
        $earliestExpiry = $earliestLicense->f_expiry_date ?? $today->format('Y-m-d');

        // Get renewed licenses (expiry after the earliest expiring one)
        $licenses = DB::connection('frontenddb')
            ->table('crm_company_license')
            ->where('f_company_id', $fId)
            ->where('f_type', 'PAID')
            ->where('status', 'Active')
            ->where('f_expiry_date', '>', $earliestExpiry)
            ->where(function($q) {
                $q->where('f_name', 'like', '%TA%')
                  ->orWhere('f_name', 'like', '%leave%')
                  ->orWhere('f_name', 'like', '%claim%')
                  ->orWhere('f_name', 'like', '%payroll%')
                  ->orWhere('f_name', 'like', '%Face & QR Code%');
            })
            ->get(['f_name', 'f_total_user', 'f_start_date', 'f_expiry_date', 'f_invoice_no', 'f_billing_cycle', 'status']);

        $invoiceGroups = [];
        $licenseSummary = [
            'attendance' => 0,
            'leave' => 0,
            'claim' => 0,
            'payroll' => 0,
        ];

        foreach ($licenses as $license) {
            $invoiceNo = $license->f_invoice_no ?? 'No Invoice';
            $quantity = $license->f_total_user;

            if (strpos($license->f_name, 'TimeTec TA') !== false) {
                $licenseSummary['attendance'] += $quantity;
            }
            if (strpos($license->f_name, 'TimeTec Leave') !== false) {
                $licenseSummary['leave'] += $quantity;
            }
            if (strpos($license->f_name, 'TimeTec Claim') !== false) {
                $licenseSummary['claim'] += $quantity;
            }
            if (strpos($license->f_name, 'TimeTec Payroll') !== false) {
                $licenseSummary['payroll'] += $quantity;
            }

            $discountRate = ($resellerLink && $resellerLink->f_rate) ? $resellerLink->f_rate : '0.00';

            if (!isset($invoiceGroups[$invoiceNo])) {
                $invoiceGroups[$invoiceNo] = [
                    'f_id' => $fId,
                    'products' => [],
                    'total_amount' => 0,
                ];
            }

            $invoiceGroups[$invoiceNo]['products'][] = [
                'f_name' => $license->f_name,
                'f_total_user' => $quantity,
                'f_total_amount' => 0,
                'f_start_date' => $license->f_start_date,
                'f_expiry_date' => $license->f_expiry_date,
                'billing_cycle' => $license->f_billing_cycle ?? 0,
                'discount' => $discountRate,
                'status' => $license->status ?? 'Active',
            ];
        }

        $invoiceGroups['_summary'] = $licenseSummary;
        $this->renewalDetails = $invoiceGroups;
        $this->showRenewalModal = true;
    }

    public function closeRenewalModal()
    {
        $this->showRenewalModal = false;
        $this->renewalDetails = [];
        $this->renewalCompanyName = '';
    }

    private function encryptCompanyId($companyId): string
    {
        $aesKey = 'Epicamera@99';
        try {
            $encrypted = openssl_encrypt($companyId, "AES-128-ECB", $aesKey);
            return base64_encode($encrypted);
        } catch (\Exception $e) {
            return $companyId;
        }
    }

    public function getExpiredWithin90DaysCountProperty()
    {
        $reseller = Auth::guard('reseller')->user();

        if (!$reseller || !$reseller->reseller_id) {
            return 0;
        }

        $today = Carbon::now();
        $ninetyDaysFromNow = Carbon::now()->addDays(90);

        $resellerLinks = DB::connection('frontenddb')
            ->table('crm_reseller_link')
            ->where('reseller_id', $reseller->reseller_id)
            ->pluck('f_id');

        return DB::connection('frontenddb')
            ->table('crm_company_license')
            ->whereIn('crm_company_license.f_company_id', $resellerLinks)
            ->where('crm_company_license.f_type', 'PAID')
            ->where('crm_company_license.status', 'Active')
            ->where(function($q) {
                $q->where('crm_company_license.f_name', 'like', '%TA%')
                  ->orWhere('crm_company_license.f_name', 'like', '%leave%')
                  ->orWhere('crm_company_license.f_name', 'like', '%claim%')
                  ->orWhere('crm_company_license.f_name', 'like', '%payroll%')
                  ->orWhere('crm_company_license.f_name', 'like', '%Face & QR Code%');
            })
            ->whereBetween('crm_company_license.f_expiry_date', [$today->format('Y-m-d'), $ninetyDaysFromNow->format('Y-m-d')])
            ->distinct('crm_company_license.f_company_id')
            ->count('crm_company_license.f_company_id');
    }

    public function getAllExpiredCountProperty()
    {
        $reseller = Auth::guard('reseller')->user();

        if (!$reseller || !$reseller->reseller_id) {
            return 0;
        }

        $today = Carbon::now();

        $resellerLinks = DB::connection('frontenddb')
            ->table('crm_reseller_link')
            ->where('reseller_id', $reseller->reseller_id)
            ->pluck('f_id');

        return DB::connection('frontenddb')
            ->table('crm_company_license')
            ->whereIn('crm_company_license.f_company_id', $resellerLinks)
            ->where('crm_company_license.f_type', 'PAID')
            ->where('crm_company_license.status', 'Active')
            ->where(function($q) {
                $q->where('crm_company_license.f_name', 'like', '%TA%')
                  ->orWhere('crm_company_license.f_name', 'like', '%leave%')
                  ->orWhere('crm_company_license.f_name', 'like', '%claim%')
                  ->orWhere('crm_company_license.f_name', 'like', '%payroll%')
                  ->orWhere('crm_company_license.f_name', 'like', '%Face & QR Code%');
            })
            ->whereDate('crm_company_license.f_expiry_date', '>=', $today->format('Y-m-d'))
            ->distinct('crm_company_license.f_company_id')
            ->count('crm_company_license.f_company_id');
    }

    public function render()
    {
        return view('livewire.reseller-expired-license', [
            'companies' => $this->companies,
            'expiredWithin90DaysCount' => $this->expiredWithin90DaysCount,
            'allExpiredCount' => $this->allExpiredCount,
            'activeTab' => $this->activeTab
        ]);
    }
}
