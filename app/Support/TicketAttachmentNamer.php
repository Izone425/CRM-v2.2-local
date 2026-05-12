<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class TicketAttachmentNamer
{
    public static function build(UploadedFile $file): string
    {
        $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $ext      = strtolower($file->getClientOriginalExtension() ?: 'bin');

        $name = self::sanitize($original);
        if ($name === '') {
            $name = 'file';
        }
        if (strlen($name) > 60) {
            $name = substr($name, 0, 60);
        }

        $date = Carbon::now()->format('dmy');

        return "{$date}_{$name}.{$ext}";
    }

    private static function sanitize(string $value): string
    {
        return preg_replace('/[^A-Za-z0-9]/', '', $value) ?? '';
    }
}
