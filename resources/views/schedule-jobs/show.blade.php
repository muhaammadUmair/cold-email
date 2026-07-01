@extends('layouts.app')

@section('page-title', 'Schedule Job Detail')

@section('content')
<div class="page-header">
    <h2>Schedule Job #{{ $scheduleJob->id }}</h2>
</div>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="mb-3">
    <a href="{{ route('schedule-jobs.edit', $scheduleJob->id) }}" class="btn btn-warning">Edit Schedule Job</a>
    <a href="{{ route('schedule-jobs.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="card bg-white p-3">
    <p><strong>Start Date:</strong> {{ $scheduleJob->start_date ? \Illuminate\Support\Carbon::parse($scheduleJob->start_date)->format('d M Y') : '-' }}</p>
    <p><strong>End Date:</strong> {{ $scheduleJob->end_date ? \Illuminate\Support\Carbon::parse($scheduleJob->end_date)->format('d M Y') : '-' }}</p>
    <p><strong>Emails Per Day:</strong> {{ $scheduleJob->emails_per_day }}</p>
    <p><strong>From Email Address:</strong> {{ $scheduleJob->from_email_address }}</p>
    <p><strong>Schedule Time:</strong> {{ $scheduleJob->schedule_time ? \Illuminate\Support\Carbon::parse($scheduleJob->schedule_time)->format('H:i') : '-' }}</p>
</div>

<a href="{{ route('schedule-jobs.index') }}" class="btn btn-secondary mt-3">Back</a>
@endsection
