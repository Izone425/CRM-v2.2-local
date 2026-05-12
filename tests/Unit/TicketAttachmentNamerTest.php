<?php

namespace Tests\Unit;

use App\Support\TicketAttachmentNamer;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\TestCase;

class TicketAttachmentNamerTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_builds_expected_shape_with_date_prefix(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 30));
        $file = UploadedFile::fake()->create('Annual Leave Policy.xlsx', 10);

        $name = TicketAttachmentNamer::build($file);

        $this->assertSame('300426_AnnualLeavePolicy.xlsx', $name);
    }

    public function test_uses_two_digit_day_month_and_year(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 1, 5));
        $file = UploadedFile::fake()->create('thing.csv', 1);

        $name = TicketAttachmentNamer::build($file);

        $this->assertStringStartsWith('050126_', $name);
    }

    public function test_strips_special_characters_from_filename(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 30));
        $file = UploadedFile::fake()->create('My File!@#$.pdf', 1);

        $name = TicketAttachmentNamer::build($file);

        $this->assertSame('300426_MyFile.pdf', $name);
    }

    public function test_caps_long_filenames(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 30));
        $longBase = str_repeat('a', 100);
        $file = UploadedFile::fake()->create("{$longBase}.txt", 1);

        $name = TicketAttachmentNamer::build($file);

        $this->assertSame('300426_' . str_repeat('a', 60) . '.txt', $name);
    }

    public function test_extension_falls_back_when_missing(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 30));
        $file = UploadedFile::fake()->create('Makefile', 1);

        $name = TicketAttachmentNamer::build($file);

        $this->assertStringEndsWith('.bin', $name);
    }

    public function test_falls_back_to_file_when_name_strips_to_empty(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 30));
        $file = UploadedFile::fake()->create('!@#$.pdf', 1);

        $name = TicketAttachmentNamer::build($file);

        $this->assertSame('300426_file.pdf', $name);
    }
}
