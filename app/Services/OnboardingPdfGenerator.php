<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\SoftwareHandover;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use setasign\Fpdi\Tcpdf\Fpdi;

class OnboardingPdfGenerator
{
    private const TEMPLATE_PATH = 'templates/software-handover/onboarding-process.pdf';
    private const EXPECTED_PAGES = 24;

    private const URL_V1 = 'www.timeteccloud.com';
    private const URL_V2 = 'www.hr2.timeteccloud.com';

    /**
     * Pages always rendered regardless of subscribed modules.
     */
    private const DEFAULT_PAGES = [1, 2, 3, 4, 5, 8, 9, 10, 11, 24];

    /**
     * Additional pages keyed by the SoftwareHandover boolean module flag.
     */
    private const MODULE_PAGES = [
        'tp' => [6, 7, 19, 20, 21, 22, 23], // Payroll
        'ta' => [12, 13, 14],               // Attendance
        'tl' => [15, 16],                   // Leave
        'tc' => [17, 18],                   // Claim
    ];

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
        // Page 4 — license activation date + login URL + temporary admin credentials.
        // URL is V1 (www.timeteccloud.com) or V2 (www.hr2.timeteccloud.com)
        // depending on SoftwareHandover.hr_version. Email & password come
        // from customers.email + customers.plain_password.
        4 => [
            'licenseDate' => [
                'box'   => ['x' => 125, 'y' => 72,  'w' => 130, 'h' => 16],
                'size'  => 18,
                'color' => [0, 144, 234],
                'padX'  => 10,
                'align' => 'L',
            ],
            'loginUrl' => [
                'box'   => ['x' => 125, 'y' => 100, 'w' => 130, 'h' => 16],
                'size'  => 18,
                'color' => [0, 144, 234],
                'padX'  => 10,
                'align' => 'L',
            ],
            'tempEmail' => [
                'box'   => ['x' => 125, 'y' => 128, 'w' => 130, 'h' => 16],
                'size'  => 18,
                'color' => [0, 144, 234],
                'padX'  => 10,
                'align' => 'L',
            ],
            'tempPassword' => [
                'box'   => ['x' => 125, 'y' => 156, 'w' => 130, 'h' => 16],
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

        $handover = $this->findHandover($customer);
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

        if ($pageCount !== self::EXPECTED_PAGES) {
            Log::warning('Onboarding master PDF page count drift', [
                'expected' => self::EXPECTED_PAGES,
                'actual'   => $pageCount,
            ]);
        }

        foreach ($this->selectPages($handover) as $pageNo) {
            if ($pageNo > $pageCount) {
                Log::warning('Skipping onboarding page beyond master length', [
                    'requested' => $pageNo,
                    'available' => $pageCount,
                ]);
                continue;
            }

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

    /**
     * Returns the sorted, deduplicated list of master PDF pages to import
     * for a given customer based on their SoftwareHandover module flags.
     * Public so unit tests can exercise the rules without rendering a PDF.
     */
    public function selectPages(?SoftwareHandover $handover): array
    {
        $pages = self::DEFAULT_PAGES;

        foreach (self::MODULE_PAGES as $flag => $extra) {
            if ($handover && $handover->{$flag}) {
                $pages = array_merge($pages, $extra);
            }
        }

        $pages = array_values(array_unique($pages));
        sort($pages);
        return $pages;
    }

    /**
     * V1 = www.timeteccloud.com, V2 = www.hr2.timeteccloud.com.
     * Defaults to V1 for null handover or any unrecognised hr_version.
     */
    public function resolveLoginUrl(?SoftwareHandover $handover): string
    {
        return ((int) ($handover?->hr_version ?? 1)) === 2
            ? self::URL_V2
            : self::URL_V1;
    }

    /**
     * Resolve the canonical SoftwareHandover row for a customer.
     * V2 customers are linked via Customer.sw_id (exact match);
     * V1 customers are linked via Customer.lead_id (latest by id).
     */
    private function findHandover(Customer $customer): ?SoftwareHandover
    {
        if ($customer->sw_id) {
            $byId = SoftwareHandover::find($customer->sw_id);
            if ($byId) {
                return $byId;
            }
        }
        if ($customer->lead_id) {
            return SoftwareHandover::where('lead_id', $customer->lead_id)
                ->orderByDesc('id')
                ->first();
        }
        return null;
    }

    public function filenameFor(Customer $customer): string
    {
        $projectCode = $this->findHandover($customer)?->project_code ?: 'handover';
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
     * - Pg 5: 5 topic cards (Topics 2-6). Each card is fully clickable; all
     *   five hit-boxes deep-link to the customer-portal Project File tab so
     *   any click inside a card (Excel pill, PDF pill, title, "Click Here >")
     *   navigates through the subscription gating.
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

        return [
            5 => [
                // Topic 2 — Project Timeline (Row 1, Col 1)
                ['box' => ['x' => 25,  'y' => 55,  'w' => 80, 'h' => 100], 'url' => $dataMigrationTab],
                // Topic 3 — User Data Migration Template (Row 1, Col 2)
                ['box' => ['x' => 110, 'y' => 55,  'w' => 80, 'h' => 100], 'url' => $dataMigrationTab],
                // Topic 4 — Request Clocking Schedule (Row 1, Col 3)
                ['box' => ['x' => 195, 'y' => 55,  'w' => 80, 'h' => 100], 'url' => $dataMigrationTab],
                // Topic 5 — Request Leave Policy (Row 2, Col 1)
                ['box' => ['x' => 65,  'y' => 160, 'w' => 80, 'h' => 45],  'url' => $dataMigrationTab],
                // Topic 6 — Request Claim Policy (Row 2, Col 2)
                ['box' => ['x' => 150, 'y' => 160, 'w' => 80, 'h' => 45],  'url' => $dataMigrationTab],
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
            // Vertically centre by shrinking the cell to the font height and
            // anchoring it at the box midpoint — TCPDF's default Cell baseline
            // sits below the visual centre of the master's rounded input box.
            $fontHeight = $spec['size'] * 0.3528; // pt → mm
            $yCentered = $spec['box']['y'] + ($spec['box']['h'] - $fontHeight) / 2;
            $pdf->SetXY($spec['box']['x'] + $padX, $yCentered);
            $pdf->Cell(
                $spec['box']['w'] - $padX * 2,
                $fontHeight,
                $value,
                0,
                0,
                $spec['align'] ?? 'L',
                false
            );
        }
    }

    /**
     * @return array{companyName:string,implementer:string,projectCode:string,licenseDate:string,tempEmail:string,tempPassword:string,loginUrl:string}
     */
    public function resolveFields(Customer $customer): array
    {
        $lead = $customer->lead;
        $handover = $this->findHandover($customer);

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
        $loginUrl     = $this->resolveLoginUrl($handover);

        return [
            'companyName'  => $companyName,
            'implementer'  => $implementer,
            'projectCode'  => $projectCode,
            'licenseDate'  => $licenseDate,
            'tempEmail'    => $tempEmail,
            'tempPassword' => $tempPassword,
            'loginUrl'     => $loginUrl,
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
