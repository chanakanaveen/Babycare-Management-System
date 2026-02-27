<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BabyVaccination;
use App\Notifications\VaccinationReminderNotification;
use Carbon\Carbon;

class SendVaccinationReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'vaccinations:send-reminders
                            {--days=3 : Number of days ahead to check for upcoming vaccinations}';

    /**
     * The console command description.
     */
    protected $description = 'Send email reminders for vaccinations scheduled within the next N days';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $now  = Carbon::today();
        $cutoff = $now->copy()->addDays($days);

        $this->info("Checking for vaccinations scheduled between {$now->toDateString()} and {$cutoff->toDateString()}...");

        // Fetch upcoming vaccinations that haven't had a reminder sent
        $upcoming = BabyVaccination::with(['baby.parentUser', 'baby.midwife', 'vaccine'])
            ->where('vaccination_status', 'scheduled')
            ->where('reminder_sent', false)
            ->whereBetween('scheduled_date', [$now, $cutoff])
            ->get();

        if ($upcoming->isEmpty()) {
            $this->info('No upcoming vaccinations require reminders.');
            return Command::SUCCESS;
        }

        $this->info("Found {$upcoming->count()} vaccination(s) requiring reminders.");

        $sentCount   = 0;
        $failedCount = 0;

        foreach ($upcoming as $vaccination) {
            $baby     = $vaccination->baby;
            $vaccine  = $vaccination->vaccine;

            if (!$baby) {
                $this->warn("Skipping record #{$vaccination->record_id}: baby not found.");
                $failedCount++;
                continue;
            }

            $babyName    = $baby->name ?? 'Unknown Baby';
            $vaccineName = $vaccine->vaccine_name ?? 'Unknown Vaccine';

            $notification = new VaccinationReminderNotification(
                $vaccination,
                $babyName,
                $vaccineName
            );

            try {
                // Notify the parent
                $parent = $baby->parentUser;
                if ($parent && $parent->email) {
                    $parent->notify($notification);
                    $this->line("  → Sent to parent [{$parent->name}] for baby [{$babyName}]");
                }

                // Notify the assigned midwife
                $midwife = $baby->midwife;
                if ($midwife && $midwife->email) {
                    $midwife->notify($notification);
                    $this->line("  → Sent to midwife [{$midwife->name}] for baby [{$babyName}]");
                }

                // Mark reminder as sent
                $vaccination->update(['reminder_sent' => true]);
                $sentCount++;
            } catch (\Exception $e) {
                $this->error("  ✗ Failed for record #{$vaccination->record_id}: {$e->getMessage()}");
                $failedCount++;
            }
        }

        $this->newLine();
        $this->info("Done. Sent: {$sentCount}, Failed: {$failedCount}");

        return $failedCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
