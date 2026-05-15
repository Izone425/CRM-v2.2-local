<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('implementer_tickets', function (Blueprint $table) {
            $table->boolean('is_kickoff_thread')->default(false)->after('software_handover_id');
            $table->index(['software_handover_id', 'is_kickoff_thread'], 'impt_handover_kickoff_idx');
        });

        DB::transaction(function () {
            // 1. Flag every existing Kick-Off ticket so the per-handover sequence
            //    assignment knows which one owns slot _0001. The Kick-Off auto-thread
            //    is the only path that sets category = 'Kick-Off Meeting' today
            //    (see ImplementerActions::mirrorTemplateEmailToThread()).
            DB::table('implementer_tickets')
                ->whereNotNull('software_handover_id')
                ->where('category', 'Kick-Off Meeting')
                ->update(['is_kickoff_thread' => true]);

            // 2. Null every existing ticket_number first. The column is uniquely
            //    indexed, so renaming in place would trip the constraint as soon
            //    as a new format value collides with an old one.
            DB::table('implementer_tickets')
                ->whereNotNull('software_handover_id')
                ->update(['ticket_number' => null]);

            // 3. Re-assign per handover: Kick-Off thread -> _0001, the rest -> _0002, _0003 ...
            $handoverIds = DB::table('implementer_tickets')
                ->whereNotNull('software_handover_id')
                ->distinct()
                ->pluck('software_handover_id');

            foreach ($handoverIds as $hid) {
                $handover = \App\Models\SoftwareHandover::find($hid);
                if (!$handover) {
                    continue;
                }
                $projectCode = $handover->project_code;

                // Kick-Off ticket(s) - keep only the first; if duplicates exist, the
                // later ones cascade into the manual-numbering loop as a safety net.
                $kickoff = DB::table('implementer_tickets')
                    ->where('software_handover_id', $hid)
                    ->where('is_kickoff_thread', true)
                    ->orderBy('id', 'asc')
                    ->first();

                if ($kickoff) {
                    DB::table('implementer_tickets')
                        ->where('id', $kickoff->id)
                        ->update(['ticket_number' => "{$projectCode}_0001"]);
                }

                // Manual tickets - numbered from 2 upward in id order
                $manualQuery = DB::table('implementer_tickets')
                    ->where('software_handover_id', $hid);

                if ($kickoff) {
                    $manualQuery->where('id', '!=', $kickoff->id);
                }

                $manualIds = $manualQuery->orderBy('id', 'asc')->pluck('id');

                $seq = 2;
                foreach ($manualIds as $tid) {
                    $padded = str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
                    DB::table('implementer_tickets')
                        ->where('id', $tid)
                        ->update(['ticket_number' => "{$projectCode}_{$padded}"]);
                    $seq++;
                }
            }

            // 4. Tickets without a software_handover_id keep the legacy IMP-YYNNNN
            //    fallback; regenerate any that got nulled in step 2 above. (Step 2
            //    only nulled rows WITH a handover, so this is a no-op safeguard.)
            $legacy = DB::table('implementer_tickets')
                ->whereNull('software_handover_id')
                ->whereNull('ticket_number')
                ->get(['id', 'created_at']);

            foreach ($legacy as $row) {
                $year = $row->created_at
                    ? \Carbon\Carbon::parse($row->created_at)->format('y')
                    : date('y');
                $padded = str_pad((string) $row->id, 4, '0', STR_PAD_LEFT);
                DB::table('implementer_tickets')
                    ->where('id', $row->id)
                    ->update(['ticket_number' => "IMP-{$year}{$padded}"]);
            }
        });
    }

    public function down(): void
    {
        // Lossy: we cannot reconstruct the original SW_{handover_id-padded-6}_IMP{seq}
        // numbers without storing them somewhere first. Restoring the column drop is
        // safe; restoring the old numbers is not, so the renumber stays.
        Schema::table('implementer_tickets', function (Blueprint $table) {
            $table->dropIndex('impt_handover_kickoff_idx');
            $table->dropColumn('is_kickoff_thread');
        });
    }
};
