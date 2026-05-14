<?php

namespace Tests\Unit;

use App\Models\SoftwareHandover;
use App\Services\OnboardingPdfGenerator;
use PHPUnit\Framework\TestCase;

class OnboardingPdfPageSelectorTest extends TestCase
{
    private OnboardingPdfGenerator $gen;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gen = new OnboardingPdfGenerator();
    }

    private function makeHandover(array $flags = []): SoftwareHandover
    {
        return new SoftwareHandover(array_merge([
            'ta' => false,
            'tl' => false,
            'tc' => false,
            'tp' => false,
        ], $flags));
    }

    public function test_no_modules_returns_default_pages(): void
    {
        $pages = $this->gen->selectPages($this->makeHandover());
        $this->assertSame([1, 2, 3, 4, 5, 8, 9, 10, 11, 24], $pages);
    }

    public function test_payroll_only_adds_6_7_19_20_21_22_23(): void
    {
        $pages = $this->gen->selectPages($this->makeHandover(['tp' => true]));
        $this->assertSame(
            [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 19, 20, 21, 22, 23, 24],
            $pages
        );
    }

    public function test_attendance_only_adds_12_13_14(): void
    {
        $pages = $this->gen->selectPages($this->makeHandover(['ta' => true]));
        $this->assertSame(
            [1, 2, 3, 4, 5, 8, 9, 10, 11, 12, 13, 14, 24],
            $pages
        );
    }

    public function test_leave_only_adds_15_16(): void
    {
        $pages = $this->gen->selectPages($this->makeHandover(['tl' => true]));
        $this->assertSame(
            [1, 2, 3, 4, 5, 8, 9, 10, 11, 15, 16, 24],
            $pages
        );
    }

    public function test_claim_only_adds_17_18(): void
    {
        $pages = $this->gen->selectPages($this->makeHandover(['tc' => true]));
        $this->assertSame(
            [1, 2, 3, 4, 5, 8, 9, 10, 11, 17, 18, 24],
            $pages
        );
    }

    public function test_all_modules_returns_full_24_page_deck(): void
    {
        $pages = $this->gen->selectPages($this->makeHandover([
            'ta' => true, 'tl' => true, 'tc' => true, 'tp' => true,
        ]));
        $this->assertSame(range(1, 24), $pages);
    }

    public function test_null_handover_returns_default_pages(): void
    {
        $pages = $this->gen->selectPages(null);
        $this->assertSame([1, 2, 3, 4, 5, 8, 9, 10, 11, 24], $pages);
    }

    public function test_result_is_sorted_ascending(): void
    {
        $pages = $this->gen->selectPages($this->makeHandover([
            'ta' => true, 'tl' => true, 'tc' => true, 'tp' => true,
        ]));
        $sorted = $pages;
        sort($sorted);
        $this->assertSame($sorted, $pages);
    }

    public function test_result_has_no_duplicates(): void
    {
        $pages = $this->gen->selectPages($this->makeHandover([
            'ta' => true, 'tl' => true, 'tc' => true, 'tp' => true,
        ]));
        $this->assertSame(array_values(array_unique($pages)), $pages);
    }

    public function test_v1_handover_yields_timeteccloud_url(): void
    {
        $h = new SoftwareHandover(['hr_version' => 1]);
        $this->assertSame('www.timeteccloud.com', $this->gen->resolveLoginUrl($h));
    }

    public function test_v2_handover_yields_hr2_timeteccloud_url(): void
    {
        $h = new SoftwareHandover(['hr_version' => 2]);
        $this->assertSame('www.hr2.timeteccloud.com', $this->gen->resolveLoginUrl($h));
    }

    public function test_null_handover_defaults_to_v1_url(): void
    {
        $this->assertSame('www.timeteccloud.com', $this->gen->resolveLoginUrl(null));
    }

    public function test_unknown_hr_version_defaults_to_v1_url(): void
    {
        $h = new SoftwareHandover(['hr_version' => 99]);
        $this->assertSame('www.timeteccloud.com', $this->gen->resolveLoginUrl($h));
    }
}
