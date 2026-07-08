<?php

use App\Services\LeadSequentialCompanyResearchProcessorService;
use App\Services\LeadSequentialEmailProcessorService;
use App\Models\ScheduleJob;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('leads:send-sequential {--once : Process only the first unsent lead}', function (LeadSequentialEmailProcessorService $processor) {
    $result = $processor->processPendingLeads((bool) $this->option('once'));

    $this->info($result['message']);
    $this->line('Processed: ' . $result['processed']);
    $this->line('Sent: ' . $result['sent']);
    $this->line('Failed: ' . $result['failed']);
})->purpose('Send unsent leads one-by-one in ascending lead id order');

Artisan::command('leads:research-sequential', function (LeadSequentialCompanyResearchProcessorService $processor) {
    $result = $processor->processNextLead();

    $this->info($result['message']);
    $this->line('Processed: ' . $result['processed']);

    if (!empty($result['lead_id'])) {
        $this->line('Lead ID: ' . $result['lead_id']);
    }

    $this->line('Success: ' . ($result['success'] ? 'yes' : 'no'));
})->purpose('Process first lead with null company_research_id in ascending lead id order');

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

Schedule::command('leads:research-sequential')
    ->everyMinute()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/leads-research-sequential.log'));
