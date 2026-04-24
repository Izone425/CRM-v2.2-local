<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Services\OnboardingPdfGenerator;
use Illuminate\Console\Command;

class CalibrateOnboardingPdf extends Command
{
    protected $signature = 'onboarding-pdf:calibrate {customerId} {--out=storage/app/onboarding-calibration.pdf}';

    protected $description = 'Render a calibration copy of the Software Onboarding PDF with red guide rectangles over every field overlay, to help dial-in coordinates in OnboardingPdfGenerator::FIELD_MAP.';

    public function handle(OnboardingPdfGenerator $generator): int
    {
        $customer = Customer::find($this->argument('customerId'));
        if (!$customer) {
            $this->error('Customer not found: ' . $this->argument('customerId'));
            return self::FAILURE;
        }

        $pdf = $generator->generateCalibration($customer);
        $out = base_path($this->option('out'));
        file_put_contents($out, $pdf);

        $this->info('Calibration PDF written: ' . $out);
        $this->line('Open it and check that every red rectangle sits exactly over a placeholder field.');
        $this->line('Adjust coordinates in OnboardingPdfGenerator::FIELD_MAP and re-run as needed.');
        return self::SUCCESS;
    }
}
