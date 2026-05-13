<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        $template = \App\Models\EmailTemplate::find(\App\Models\EmailTemplate::KICK_OFF_TEMPLATE_ID);
        if (!$template) {
            return;
        }

        $old = 'You can also access the data files via the';
        $new = 'You can also access the project files via the';

        if (str_contains((string) $template->content, $old)) {
            $template->content = str_replace($old, $new, $template->content);
            $template->save();
        }
    }

    public function down(): void
    {
        $template = \App\Models\EmailTemplate::find(\App\Models\EmailTemplate::KICK_OFF_TEMPLATE_ID);
        if (!$template) {
            return;
        }

        $old = 'You can also access the project files via the';
        $new = 'You can also access the data files via the';

        if (str_contains((string) $template->content, $old)) {
            $template->content = str_replace($old, $new, $template->content);
            $template->save();
        }
    }
};
