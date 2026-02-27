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
    protected $description = 'Send report reminders to adopters and overdue alerts to owners';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $today = Carbon::today();
        $sevenDaysFromNow = $today->copy()->addDays(7)->toDateString();
        $twoDaysFromNow = $today->copy()->addDays(2)->toDateString();

        // Overdue trigger dates: exactly 1, 3, 7 days ago
        $overdueDates = [
            $today->copy()->subDays(1)->toDateString(),
            $today->copy()->subDays(3)->toDateString(),
            $today->copy()->subDays(7)->toDateString(),
        ];

        $cases = AdoptionCase::with('pet')
            ->where('status', AdoptionCase::STATUS_ACTIVE)
            ->whereNotNull('tracking_config')
            ->whereNotNull('next_report_due_at')
            ->where(function ($query) use ($sevenDaysFromNow, $twoDaysFromNow, $overdueDates) {
                $query
                    // Adopter reminders: precisely 7 days, or continuously <= 2 days
                    ->where(\DB::raw('DATE(next_report_due_at)'), $sevenDaysFromNow)
                    ->orWhere(\DB::raw('DATE(next_report_due_at)'), '<=', $twoDaysFromNow)
                    // Owner overdue alerts: precisely 1, 3, 7 days overdue
                    ->orWhereIn(\DB::raw('DATE(next_report_due_at)'), $overdueDates);
            })
            ->get();

        if ($cases->isEmpty()) {
            $this->info('No reminders or alerts to send today.');
            return 0;
        }

        $remindersSent = 0;
        $overdueAlertsSent = 0;

        foreach ($cases as $case) {
            $dueDate = Carbon::parse($case->next_report_due_at)->startOfDay();
            $daysUntilDue = (int) $today->diffInDays($dueDate, false);

            // --- Adopter reminders (due date is in the future or today) ---
            if ($daysUntilDue === 7 || ($daysUntilDue >= 0 && $daysUntilDue <= 2)) {
                $notification = $notificationService->notifyReportDueReminder($case, $daysUntilDue);
                if ($notification) {
                    $remindersSent++;
                }
            }

            // --- Owner overdue alerts (due date has passed) ---
            if ($daysUntilDue < 0) {
                $daysOverdue = abs($daysUntilDue);

                // Only trigger on exact days: 1, 3, 7
                if (in_array($daysOverdue, [1, 3, 7])) {
                    $notification = $notificationService->notifyReportOverdueToOwner($case, $daysOverdue);
                    if ($notification) {
                        $overdueAlertsSent++;
                    }
                }
            }
        }

        $this->info("Adopter reminders sent: {$remindersSent}");
        $this->info("Owner overdue alerts sent: {$overdueAlertsSent}");
        $this->info("Total cases processed: {$cases->count()}");
        return 0;
    }
}
