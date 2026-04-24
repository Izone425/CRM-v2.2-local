<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\SoftwareHandover;
use Carbon\Carbon;
use RuntimeException;
use setasign\Fpdi\Tcpdf\Fpdi;

class OnboardingPdfGenerator
{
    private const TEMPLATE_PATH = 'templates/software-handover/onboarding-process.pdf';
    private const EXPECTED_PAGES = 29;

    /**
     * Field overlays per page. All coordinates are millimetres from the top-left
     * corner of a landscape-A4 page (297 × 210 mm).
     *
     * Each entry defines the white mask rectangle that erases the baked-in
     * placeholder text, plus the font/color/alignment used to draw the
     * personalized value centred inside the same rectangle.
     *
     * Calibrated against the TimeTec onboarding template at
     * storage/app/templates/software-handover/onboarding-process.pdf.
     * If you replace the template, re-run `php artisan onboarding-pdf:calibrate`.
     */
    private const FIELD_MAP = [
        2 => [
            'companyName' => [
                'box'   => ['x' => 104, 'y' => 79, 'w' => 145, 'h' => 16],
                'size'  => 18,
                'color' => [198, 40, 40],
                'padX'  => 2,
                'align' => 'L',
            ],
            'implementer' => [
                'box'   => ['x' => 104, 'y' => 97, 'w' => 145, 'h' => 16],
                'size'  => 18,
                'color' => [198, 40, 40],
                'padX'  => 2,
                'align' => 'L',
            ],
            'projectCode' => [
                'box'   => ['x' => 104, 'y' => 115, 'w' => 145, 'h' => 16],
                'size'  => 18,
                'color' => [198, 40, 40],
                'padX'  => 2,
                'align' => 'L',
            ],
        ],
        5 => [
            'licenseDate' => [
                'box'   => ['x' => 125, 'y' => 72, 'w' => 130, 'h' => 15],
                'size'  => 18,
                'color' => [0, 144, 234],
                'padX'  => 10,
                'align' => 'L',
            ],
            'tempEmail' => [
                'box'   => ['x' => 125, 'y' => 123, 'w' => 130, 'h' => 16],
                'size'  => 18,
                'color' => [0, 144, 234],
                'padX'  => 10,
                'align' => 'L',
            ],
            'tempPassword' => [
                'box'   => ['x' => 125, 'y' => 150, 'w' => 130, 'h' => 16],
                'size'  => 18,
                'color' => [0, 144, 234],
                'padX'  => 10,
                'align' => 'L',
            ],
        ],
    ];

    public function generate(Customer $customer): string
    {
        $templatePath = storage_path('app/' . self::TEMPLATE_PATH);

        if (!file_exists($templatePath)) {
            throw new RuntimeException(
                'Onboarding template PDF not found at ' . $templatePath
                . '. Place the TimeTec onboarding PDF there and retry.'
            );
        }

        $fields = $this->resolveFields($customer);
        $linkMap = $this->buildLinkMap();

        $pdf = new Fpdi('L', 'mm', 'A4');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetCreator('TT CRM v2');
        $pdf->SetTitle('Software Onboarding — ' . $fields['projectCode']);

        $pageCount = $pdf->setSourceFile($templatePath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tplId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($tplId);

            $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
            $pdf->AddPage($orientation, [$size['width'], $size['height']]);
            $pdf->useTemplate($tplId, 0, 0, $size['width'], $size['height']);

            if (isset(self::FIELD_MAP[$pageNo])) {
                $this->drawOverlays($pdf, self::FIELD_MAP[$pageNo], $fields);
            }

            if (isset($linkMap[$pageNo])) {
                $this->drawLinks($pdf, $linkMap[$pageNo]);
            }
        }

        return $pdf->Output('', 'S');
    }

    public function filenameFor(Customer $customer): string
    {
        $projectCode = optional($customer->softwareHandover)->project_code ?: 'handover';
        return "Software_Onboarding_{$projectCode}.pdf";
    }

    /**
     * Used by the calibration artisan command to render a diagnostic PDF with
     * thin red outlines where each overlay will land. Not used by the customer.
     */
    public function generateCalibration(Customer $customer): string
    {
        $templatePath = storage_path('app/' . self::TEMPLATE_PATH);
        if (!file_exists($templatePath)) {
            throw new RuntimeException('Template missing: ' . $templatePath);
        }

        $pdf = new Fpdi('L', 'mm', 'A4');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(0, 0, 0);

        $pageCount = $pdf->setSourceFile($templatePath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tplId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($tplId);
            $pdf->AddPage(($size['width'] > $size['height']) ? 'L' : 'P', [$size['width'], $size['height']]);
            $pdf->useTemplate($tplId, 0, 0, $size['width'], $size['height']);

            if (isset(self::FIELD_MAP[$pageNo])) {
                foreach (self::FIELD_MAP[$pageNo] as $key => $spec) {
                    $pdf->SetDrawColor(255, 0, 0);
                    $pdf->SetLineWidth(0.3);
                    $pdf->Rect($spec['box']['x'], $spec['box']['y'], $spec['box']['w'], $spec['box']['h']);
                    $pdf->SetFont('helvetica', '', 8);
                    $pdf->SetTextColor(255, 0, 0);
                    $pdf->Text($spec['box']['x'], max(0, $spec['box']['y'] - 1), $key);
                }
            }
        }

        return $pdf->Output('', 'S');
    }

    /**
     * Clickable regions per page (landscape A4, millimetres from top-left).
     *
     * - Pg 6: 5 topic cards × 1-2 file buttons. 4 Excel buttons stream the
     *   existing V1 templates via `customer.onboarding-template.download`.
     *   The Project Timeline Excel (no file on disk) and the User Data
     *   Migration PDF (no PDF on disk) fall back to the Data Migration tab.
     * - Pg 10: 6 "Click Here" buttons (3 modules × 2 rows) all deep-link
     *   to the Webinar/Training tab since TrainerFile records aren't seeded.
     *
     * Coordinates are best-fit estimates over the v2 template — nudge if a
     * click hit-box misses the visual button.
     */
    private function buildLinkMap(): array
    {
        $dashboard = route('customer.dashboard');
        $dataMigrationTab = $dashboard . '?tab=dataMigration';
        $webinarTab = $dashboard . '?tab=webinar';

        $tmpl = fn(string $key) => route('customer.onboarding-template.download', ['key' => $key]);

        return [
            6 => [
                // Row 1 — Topic 2: Project Timeline (no file → fallback to tab)
                ['box' => ['x' => 38,  'y' => 102, 'w' => 50, 'h' => 14], 'url' => $dataMigrationTab],
                // Row 1 — Topic 3: User Data Migration — Excel (direct)
                ['box' => ['x' => 123, 'y' => 98,  'w' => 50, 'h' => 14], 'url' => $tmpl('user-data-migration')],
                // Row 1 — Topic 3: User Data Migration — PDF (no file → fallback)
                ['box' => ['x' => 123, 'y' => 114, 'w' => 50, 'h' => 14], 'url' => $dataMigrationTab],
                // Row 1 — Topic 4: Request Clocking Schedule (direct)
                ['box' => ['x' => 208, 'y' => 102, 'w' => 50, 'h' => 14], 'url' => $tmpl('clocking-schedule')],
                // Row 2 — Topic 5: Request Leave Policy (direct)
                ['box' => ['x' => 80,  'y' => 182, 'w' => 50, 'h' => 14], 'url' => $tmpl('leave-policy')],
                // Row 2 — Topic 6: Request Claim Policy (direct)
                ['box' => ['x' => 165, 'y' => 182, 'w' => 50, 'h' => 14], 'url' => $tmpl('claim-policy')],
            ],
            10 => [
                // Col 1 Attendance — upper / lower buttons
                ['box' => ['x' => 55,  'y' => 107, 'w' => 55, 'h' => 16], 'url' => $webinarTab],
                ['box' => ['x' => 55,  'y' => 135, 'w' => 55, 'h' => 16], 'url' => $webinarTab],
                // Col 2 Leave & Claim — upper / lower buttons
                ['box' => ['x' => 122, 'y' => 107, 'w' => 55, 'h' => 16], 'url' => $webinarTab],
                ['box' => ['x' => 122, 'y' => 135, 'w' => 55, 'h' => 16], 'url' => $webinarTab],
                // Col 3 Payroll — upper / lower buttons
                ['box' => ['x' => 189, 'y' => 107, 'w' => 55, 'h' => 16], 'url' => $webinarTab],
                ['box' => ['x' => 189, 'y' => 135, 'w' => 55, 'h' => 16], 'url' => $webinarTab],
            ],
        ];
    }

    private function drawLinks(Fpdi $pdf, array $pageLinks): void
    {
        foreach ($pageLinks as $link) {
            $pdf->Link(
                $link['box']['x'],
                $link['box']['y'],
                $link['box']['w'],
                $link['box']['h'],
                $link['url']
            );
        }
    }

    private function drawOverlays(Fpdi $pdf, array $pageMap, array $fields): void
    {
        $pdf->SetCellPadding(0);

        foreach ($pageMap as $key => $spec) {
            $value = $fields[$key] ?? '';

            [$r, $g, $b] = $spec['color'];
            $pdf->SetFont('helvetica', 'B', $spec['size']);
            $pdf->SetTextColor($r, $g, $b);

            $padX = $spec['padX'] ?? 2;
            $pdf->SetXY($spec['box']['x'] + $padX, $spec['box']['y']);
            $pdf->Cell(
                $spec['box']['w'] - $padX * 2,
                $spec['box']['h'],
                $value,
                0,
                0,
                $spec['align'] ?? 'L',
                false
            );
        }
    }

    /**
     * @return array{companyName:string,implementer:string,projectCode:string,licenseDate:string,tempEmail:string,tempPassword:string}
     */
    public function resolveFields(Customer $customer): array
    {
        $lead = $customer->lead;
        $handover = $customer->lead_id
            ? SoftwareHandover::where('lead_id', $customer->lead_id)->orderByDesc('id')->first()
            : null;

        $companyName = $customer->company_name
            ?: optional($lead)->company_name
            ?: optional($handover)->company_name
            ?: 'Your Company';

        $implementer = optional($handover)->implementer ?: 'To be assigned';

        $projectCode = optional($handover)->project_code ?: '—';

        $kickOff = null;
        if ($lead && method_exists($lead, 'implementerAppointment')) {
            $kickOff = optional(
                $lead->implementerAppointment()
                    ->where('type', 'KICK OFF MEETING SESSION')
                    ->orderBy('date')
                    ->first()
            )->date;
        }
        if (!$kickOff) {
            $kickOff = optional($handover)->kick_off_meeting;
        }
        $licenseDate = $kickOff
            ? Carbon::parse($kickOff)->format('d/m/Y')
            : 'To be confirmed';

        $tempEmail    = $customer->email ?: '—';
        $tempPassword = $customer->plain_password ?: '—';

        return [
            'companyName'  => $companyName,
            'implementer'  => $implementer,
            'projectCode'  => $projectCode,
            'licenseDate'  => $licenseDate,
            'tempEmail'    => $tempEmail,
            'tempPassword' => $tempPassword,
        ];
    }

    public function hasCompleteData(Customer $customer): bool
    {
        $f = $this->resolveFields($customer);
        return $f['licenseDate']  !== 'To be confirmed'
            && $f['implementer']  !== 'To be assigned'
            && $f['tempEmail']    !== '—'
            && $f['tempPassword'] !== '—';
    }
}
