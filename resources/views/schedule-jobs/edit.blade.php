@extends('layouts.app')

@section('page-title', 'Edit Schedule Job')

@section('content')
@php
    $today = now()->toDateString();
    $startDateValue = $scheduleJob->start_date ? \Illuminate\Support\Carbon::parse($scheduleJob->start_date)->toDateString() : $today;
    $endDateValue = $scheduleJob->end_date ? \Illuminate\Support\Carbon::parse($scheduleJob->end_date)->toDateString() : now()->addYear()->toDateString();
    $scheduleTimeValue = $scheduleJob->schedule_time ? \Illuminate\Support\Carbon::parse($scheduleJob->schedule_time)->format('H:i') : '';
@endphp

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2>Edit Schedule Job</h2>
        <p>Update schedule job details</p>
    </div>
    <a href="{{ route('schedule-jobs.show', $scheduleJob->id) }}" class="btn btn-secondary">Back to Schedule Job</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">Please fix the highlighted fields and try again.</div>
@endif

<form method="POST" action="{{ route('schedule-jobs.update', $scheduleJob->id) }}">
    @csrf
    @method('PUT')

    <div class="card mb-4">
        <div class="card-header"><strong>Record Info</strong></div>
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label class="form-label">ID</label>
                <input type="text" class="form-control" value="{{ $scheduleJob->id }}" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Created At</label>
                <input type="text" class="form-control" value="{{ $scheduleJob->created_at }}" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Updated At</label>
                <input type="text" class="form-control" value="{{ $scheduleJob->updated_at }}" readonly>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><strong>Schedule Job Details</strong></div>
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" min="{{ $today }}" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $startDateValue) }}">
                @error('start_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" min="{{ $today }}" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $endDateValue) }}">
                @error('end_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Emails Per Day</label>
                <input type="number" name="emails_per_day" min="1" class="form-control @error('emails_per_day') is-invalid @enderror" value="{{ old('emails_per_day', $scheduleJob->emails_per_day) }}">
                @error('emails_per_day')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">From Email Address</label>
                <select name="from_email_address" class="form-select @error('from_email_address') is-invalid @enderror">
                    <option value="">Select an email address</option>
                    @foreach ($fromEmailOptions as $email)
                        <option value="{{ $email }}" @selected(old('from_email_address', $scheduleJob->from_email_address) === $email)>{{ $email }}</option>
                    @endforeach
                </select>
                @error('from_email_address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Schedule Time</label>
                <input type="time" name="schedule_time" class="form-control @error('schedule_time') is-invalid @enderror" value="{{ old('schedule_time', $scheduleTimeValue) }}">
                @error('schedule_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('schedule-jobs.show', $scheduleJob->id) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection
