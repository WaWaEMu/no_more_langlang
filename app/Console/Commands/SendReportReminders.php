<?php

namespace App\Console\Commands;

use App\Models\AdoptionCase;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendReportReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tracking:send-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Send reminder notifications to adopters 7 days and 2 days before tracking reports are due';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $today = Carbon::today();
        $sevenDaysFromNow = $today->copy()->addDays(7)->toDateString();
        $twoDaysFromNow = $today->copy()->addDays(2)->toDateString();

        $dueCases = AdoptionCase::with('pet')
            ->where('status', AdoptionCase::STATUS_ACTIVE)
            ->whereNotNull('tracking_config')
            ->whereNotNull('next_report_due_at')
            ->where(function($query) use ($sevenDaysFromNow, $twoDaysFromNow) {
                // Trigger precisely at 7 days, or continuously if <= 2 days
                $query->where(\DB::raw('DATE(next_report_due_at)'), $sevenDaysFromNow)
                      ->orWhere(\DB::raw('DATE(next_report_due_at)'), '<=', $twoDaysFromNow);
            })
            ->get();

        if ($dueCases->isEmpty()) {
            $this->info('No report reminders to send today based on the 7/2 rule.');
            return 0;
        }

        $sent = 0;
        foreach ($dueCases as $case) {
            $dueDate = Carbon::parse($case->next_report_due_at)->startOfDay();
            $daysUntilDue = (int) $today->diffInDays($dueDate, false);

            // Trigger if exactly 7 days remaining, OR if it's the final 2-day stretch
            if ($daysUntilDue === 7 || $daysUntilDue <= 2) {
                $notification = $notificationService->notifyReportDueReminder($case, max(0, $daysUntilDue));
                if ($notification) {
                    $sent++;
                }
            }
        }

        $this->info("Sent {$sent} report reminder(s) out of {$dueCases->count()} identified case(s).");
        return 0;
    }
}
