@extends('layouts.app')

@section('page-title', 'Schedule Jobs')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2>Schedule Jobs</h2>
        <p>Email scheduling configuration records</p>
    </div>
    <a href="{{ route('schedule-jobs.create') }}" class="btn btn-primary">+ Add Schedule Job</a>
</div>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<table class="table table-bordered bg-white">
    <thead>
        <tr>
            <th>#</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Emails/Day</th>
            <th>From Email</th>
            <th>Schedule Time</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($scheduleJobs as $job)
            <tr>
                <td>{{ $job->id }}</td>
                <td>{{ $job->start_date ? \Illuminate\Support\Carbon::parse($job->start_date)->format('d M Y') : '-' }}</td>
                <td>{{ $job->end_date ? \Illuminate\Support\Carbon::parse($job->end_date)->format('d M Y') : '-' }}</td>
                <td>{{ $job->emails_per_day }}</td>
                <td>{{ $job->from_email_address }}</td>
                <td>{{ $job->schedule_time ? \Illuminate\Support\Carbon::parse($job->schedule_time)->format('H:i') : '-' }}</td>
                <td>
                    <a href="{{ route('schedule-jobs.show', $job->id) }}" class="btn btn-sm btn-primary">View</a>
                    <a href="{{ route('schedule-jobs.edit', $job->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('schedule-jobs.destroy', $job->id) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center">No records found.</td></tr>
        @endforelse
    </tbody>
</table>

{{ $scheduleJobs->links() }}
@endsection
