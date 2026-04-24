<?php

namespace App\Http\Controllers;

use App\Services\OnboardingPdfGenerator;
use Illuminate\Http\Response;

class CustomerOnboardingPdfController extends Controller
{
    public function __construct(private OnboardingPdfGenerator $generator)
    {
    }

    public function show(): Response
    {
        $customer = auth('customer')->user();
        abort_unless($customer, 403);

        $pdf = $this->generator->generate($customer);
        $filename = $this->generator->filenameFor($customer);

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control'       => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function download(): Response
    {
        $customer = auth('customer')->user();
        abort_unless($customer, 403);

        $pdf = $this->generator->generate($customer);
        $filename = $this->generator->filenameFor($customer);

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'private, no-store, max-age=0',
        ]);
    }
}
