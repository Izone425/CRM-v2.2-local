<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ResellerHandoverFe;

class ResellerRenewalRequestFe extends Component
{
    public $showModal = false;
    public $search = '';
    public $selectedSubscriber = null;
    public $subscriberStatus = 'active';
    public $attendance = 0;
    public $leave = 0;
    public $claim = 0;
    public $payroll = 0;
    public $qf_master = 0;
    public $category = '';
    public $resellerRemark = '';
    public $headcountError = '';

    public function updatedResellerRemark($value)
    {
        $this->resellerRemark = strtoupper($value);
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->resetFields();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->search = '';
        $this->selectedSubscriber = null;
        $this->subscriberStatus = 'active';
        $this->attendance = 0;
        $this->leave = 0;
        $this->claim = 0;
        $this->payroll = 0;
        $this->qf_master = 0;
        $this->category = '';
        $this->resellerRemark = '';
        $this->headcountError = '';
    }

    public function selectSubscriber($fId, $companyName)
    {
        $this->selectedSubscriber = [
            'f_id' => $fId,
            'company_name' => strtoupper($companyName)
        ];
        $this->search = strtoupper($companyName);
    }

    public function clearSubscriber()
    {
        $this->selectedSubscriber = null;
        $this->search = '';
    }

    public function getSubscribersProperty()
    {
        $reseller = Auth::guard('reseller')->user();

        if (!$reseller || !$reseller->reseller_id) {
            return collect([]);
        }

        $query = DB::connection('frontenddb')
            ->table('crm_reseller_link')
            ->join('crm_customer', 'crm_reseller_link.f_backend_companyid', '=', 'crm_customer.f_backend_companyid')
            ->select(
                'crm_reseller_link.f_id',
                'crm_reseller_link.f_company_name',
                'crm_customer.f_status'
            )
            ->where('crm_reseller_link.reseller_id', $reseller->reseller_id);

        if (strlen($this->search) > 0) {
            $query->where('crm_reseller_link.f_company_name', 'like', '%' . $this->search . '%');
        }

        if ($this->subscriberStatus === 'active') {
            $query->where('crm_customer.f_status', 'A');
        } else {
            $query->whereIn('crm_customer.f_status', ['D', 'I', 'T']);
        }

        return $query->orderBy('crm_reseller_link.f_company_name', 'asc')->limit(50)->get();
    }

    public function submitRequest()
    {
        $this->headcountError = '';

        $this->validate([
            'selectedSubscriber' => 'required',
            'category' => 'required|in:renewal_subscription,addon_headcount',
            'attendance' => 'required|integer|min:0',
            'leave' => 'required|integer|min:0',
            'claim' => 'required|integer|min:0',
            'payroll' => 'required|integer|min:0',
            'qf_master' => 'required|integer|min:0',
            'resellerRemark' => 'nullable|string|max:1000',
        ]);

        if ($this->attendance == 0 && $this->leave == 0 && $this->claim == 0 && $this->payroll == 0 && $this->qf_master == 0) {
            $this->headcountError = 'Please enter at least 1 headcount for any product (Attendance, Leave, Claim, Payroll, or QF Master).';
            return;
        }

        $reseller = Auth::guard('reseller')->user();

        ResellerHandoverFe::create([
            'reseller_id' => $reseller->reseller_id,
            'reseller_name' => $reseller->name,
            'reseller_company_name' => $reseller->company_name ?? '',
            'subscriber_id' => $this->selectedSubscriber['f_id'],
            'subscriber_name' => $this->selectedSubscriber['company_name'],
            'subscriber_status' => $this->subscriberStatus === 'active' ? 'A' : 'I',
            'category' => $this->category,
            'attendance_qty' => $this->attendance,
            'leave_qty' => $this->leave,
            'claim_qty' => $this->claim,
            'payroll_qty' => $this->payroll,
            'qf_master_qty' => $this->qf_master,
            'reseller_remark' => $this->resellerRemark,
            'status' => 'new',
        ]);

        $this->dispatch('fe-handover-updated');
        $this->dispatch('notify', message: 'FE request submitted successfully!', type: 'success');
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.reseller-renewal-request-fe', [
            'subscribers' => $this->subscribers
        ]);
    }
}
