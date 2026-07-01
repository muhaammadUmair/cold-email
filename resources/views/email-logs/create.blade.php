@extends('layouts.app')

@section('page-title', 'Add Email Log')

@section('content')
@php
    $sentAtValue = '';
@endphp

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2>Add Email Log</h2>
        <p>Create a new email log record</p>
    </div>
    <a href="{{ route('email-logs.index') }}" class="btn btn-secondary">Back to Logs</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">Please fix the highlighted fields and try again.</div>
@endif

<form method="POST" action="{{ route('email-logs.store') }}">
    @csrf

    <div class="card mb-4">
        <div class="card-header"><strong>Email Log Details</strong></div>
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label class="form-label">Lead</label>
                <select name="lead_id" class="form-select @error('lead_id') is-invalid @enderror">
                    <option value="">Select a lead</option>
                    @foreach ($leads as $lead)
                        <option value="{{ $lead->id }}" @selected(old('lead_id') == $lead->id)>
                            {{ $lead->first_name }} {{ $lead->last_name }} ({{ $lead->email }})
                        </option>
                    @endforeach
                </select>
                @error('lead_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Status</label>
                <input type="text" name="status" class="form-control @error('status') is-invalid @enderror" value="{{ old('status') }}">
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Gmail Message ID</label>
                <input type="text" name="gmail_message_id" class="form-control @error('gmail_message_id') is-invalid @enderror" value="{{ old('gmail_message_id') }}">
                @error('gmail_message_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Sent At</label>
                <input type="datetime-local" name="sent_at" class="form-control @error('sent_at') is-invalid @enderror" value="{{ old('sent_at', $sentAtValue) }}">
                @error('sent_at')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('email-logs.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection