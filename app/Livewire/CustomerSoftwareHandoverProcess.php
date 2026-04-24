<?php

namespace App\Livewire;

use App\Services\OnboardingPdfGenerator;
use Livewire\Component;

class CustomerSoftwareHandoverProcess extends Component
{
    public function render()
    {
        $customer = auth('customer')->user();
        $context = null;
        $hasCompleteData = false;
        $templateMissing = false;

        if ($customer) {
            $generator = app(OnboardingPdfGenerator::class);
            $context = $generator->resolveFields($customer);
            $hasCompleteData = $generator->hasCompleteData($customer);
            $templateMissing = !file_exists(storage_path('app/templates/software-handover/onboarding-process.pdf'));
        }

        return view('livewire.customer-software-handover-process', [
            'context'         => $context,
            'hasCompleteData' => $hasCompleteData,
            'templateMissing' => $templateMissing,
        ]);
    }
}
