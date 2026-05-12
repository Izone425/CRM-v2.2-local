<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        $template = \App\Models\EmailTemplate::find(\App\Models\EmailTemplate::KICK_OFF_TEMPLATE_ID);
        if (!$template) {
            return;
        }

        if (!str_contains((string) $template->content, '{customer_portal_data_file_link}')) {
            $needle = 'Software Onboarding PDF attached (please refer to page 07).';
            $insertion = '<br>You can also access the data files via the {customer_portal_data_file_link}.';

            if (str_contains((string) $template->content, $needle)) {
                $template->content = str_replace($needle, $needle . $insertion, $template->content);
            } else {
                $template->content .= '<p>You can also access the data files via the {customer_portal_data_file_link}.</p>';
            }
        }

        if (empty($template->thread_label)) {
            $template->thread_label = 'Kick-Off Summary';
        }

        $template->save();
    }

    public function down(): void
    {
    }
};
