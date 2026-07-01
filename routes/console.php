<?php

use App\Models\ScheduleJob;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('schedule-jobs:run', function () {
    $timezone = 'EST';
    $now = Carbon::now($timezone);
    $today = $now->toDateString();
    $currentTime = $now->format('H:i');

    Log::info('Schedule job tick started.', [
        'now_est' => $now->toDateTimeString(),
        'timezone' => $timezone,
        'today' => $today,
        'current_time' => $currentTime,
    ]);

    $jobs = ScheduleJob::query()
        ->whereDate('start_date', '<=', $today)
        ->whereDate('end_date', '>=', $today)
        ->whereTime('schedule_time', '=', $currentTime)
        ->get();

    if ($jobs->isEmpty()) {
        Log::info('No schedule jobs due for this minute.', [
            'checked_time' => $currentTime,
            'checked_date' => $today,
        ]);

        $this->info('No due schedule jobs.');
        return;
    }

    foreach ($jobs as $job) {
        Log::info('Schedule job due and executing.', [
            'job_id' => $job->id,
            'scheduled_time' => $job->schedule_time,
            'executed_at_est' => $now->toDateTimeString(),
            'timezone' => $timezone,
            'emails_per_day' => $job->emails_per_day,
            'from_email_address' => $job->from_email_address,
        ]);

        // Placeholder for actual execution logic (email send queue / processing function).
    }

    $this->info('Executed ' . $jobs->count() . ' schedule job(s).');
})->purpose('Run schedule jobs due for the current minute');

Schedule::command('schedule-jobs:run')
    ->everyMinute()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/schedule-jobs-runner.log'));
