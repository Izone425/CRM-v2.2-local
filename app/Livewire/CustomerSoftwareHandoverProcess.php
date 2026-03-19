<?php

namespace App\Livewire;

use App\Models\SoftwareHandoverProcessFile;
use Livewire\Component;

class CustomerSoftwareHandoverProcess extends Component
{
    public function downloadFile(int $fileId)
    {
        $customer = auth('customer')->user();
        if (!$customer) {
            abort(403);
        }

        $file = SoftwareHandoverProcessFile::where('id', $fileId)
            ->where('lead_id', $customer->lead_id)
            ->firstOrFail();

        return redirect()->route('customer.software-handover-process.download', $file);
    }

    public function render()
    {
        $customer = auth('customer')->user();
        $files = collect();

        if ($customer) {
            $files = SoftwareHandoverProcessFile::where('lead_id', $customer->lead_id)
                ->with('uploader')
                ->orderBy('version', 'desc')
                ->get();
        }

        return view('livewire.customer-software-handover-process', [
            'files' => $files,
        ]);
    }
}
