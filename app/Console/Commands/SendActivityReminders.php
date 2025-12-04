<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Activities\Models\Activity;
use App\Models\User;
use App\Notifications\ActivityReminderNotification;
use Carbon\Carbon;

class SendActivityReminders extends Command
{
    protected $signature = 'crm:activity-reminders';
    protected $description = 'Send daily reminders about overdue and today activities';

    public function handle(): int
    {
        $today = Carbon::today();

        // Get all users who have activities (you can scope by tenant if needed)
        $userIds = Activity::query()
            ->select('owner_id')
            ->whereNotNull('owner_id')
            ->distinct()
            ->pluck('owner_id');

        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            $base = Activity::where('account_id', $user->account_id ?? null)
                ->where('tenant_id', $user->tenant_id ?? null)
                ->where('owner_id', $user->id)
                ->where('is_completed', false);

            $overdueCount = (clone $base)
                ->whereDate('due_date', '<', $today)
                ->count();

            $todayCount = (clone $base)
                ->whereDate('due_date', '=', $today)
                ->count();

            if ($overdueCount === 0 && $todayCount === 0) {
                continue;
            }

            $user->notify(new ActivityReminderNotification($overdueCount, $todayCount));
        }

        $this->info('Activity reminders sent.');
        return Command::SUCCESS;
    }
}
