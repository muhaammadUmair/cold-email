<?php

namespace App\Http\Controllers;

use App\Models\ScheduleJob;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScheduleJobController extends Controller
{
    public function create()
    {
        $scheduleJob = new ScheduleJob();
        $fromEmailOptions = $this->fromEmailOptions();

        return view('schedule-jobs.create', compact('scheduleJob', 'fromEmailOptions'));
    }

    public function store(Request $request)
    {
        $fromEmailOptions = $this->fromEmailOptions();

        $validated = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'emails_per_day' => ['required', 'integer', 'min:1'],
            'from_email_address' => ['required', 'email', Rule::in($fromEmailOptions)],
            'schedule_time' => ['required', 'date_format:H:i'],
        ]);

        $scheduleJob = ScheduleJob::create($validated);

        return redirect()->route('schedule-jobs.show', $scheduleJob)->with('status', 'Schedule job created successfully.');
    }

    public function index()
    {
        $scheduleJobs = ScheduleJob::latest()->paginate(15);

        return view('schedule-jobs.index', compact('scheduleJobs'));
    }

    public function show(string $id)
    {
        $scheduleJob = ScheduleJob::findOrFail($id);

        return view('schedule-jobs.show', compact('scheduleJob'));
    }

    public function edit(string $id)
    {
        $scheduleJob = ScheduleJob::findOrFail($id);
        $fromEmailOptions = $this->fromEmailOptions($scheduleJob->from_email_address);

        return view('schedule-jobs.edit', compact('scheduleJob', 'fromEmailOptions'));
    }

    public function update(Request $request, string $id)
    {
        $scheduleJob = ScheduleJob::findOrFail($id);
        $fromEmailOptions = $this->fromEmailOptions($scheduleJob->from_email_address);

        $validated = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'emails_per_day' => ['required', 'integer', 'min:1'],
            'from_email_address' => ['required', 'email', Rule::in($fromEmailOptions)],
            'schedule_time' => ['required', 'date_format:H:i'],
        ]);

        $scheduleJob->update($validated);

        return redirect()->route('schedule-jobs.show', $scheduleJob)->with('status', 'Schedule job updated successfully.');
    }

    public function destroy(string $id)
    {
        ScheduleJob::findOrFail($id)->delete();

        return redirect()->route('schedule-jobs.index')->with('status', 'Record deleted.');
    }

    protected function fromEmailOptions(?string $selected = null): array
    {
        $options = [
            config('mail.from.address'),
            auth()->user()?->email,
            $selected,
        ];

        $filtered = array_values(array_unique(array_filter($options)));

        if (empty($filtered)) {
            return ['noreply@example.com'];
        }

        return $filtered;
    }
}
