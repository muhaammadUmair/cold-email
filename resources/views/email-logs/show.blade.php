@extends('layouts.app')

@section('page-title', 'Email Log Detail')

@section('content')
<div class="page-header">
    <h2>Email Log #{{ $emailLog->id }}</h2>
</div>

<div class="card bg-white p-3">
    <p><strong>Lead:</strong> {{ $emailLog->lead->first_name ?? '-' }} {{ $emailLog->lead->last_name ?? '' }}</p>
    <p><strong>Status:</strong> {{ $emailLog->status ?? '-' }}</p>
    <p><strong>Gmail Message ID:</strong> {{ $emailLog->gmail_message_id ?? '-' }}</p>
    <p><strong>Sent At:</strong> {{ $emailLog->sent_at ?? '-' }}</p>
</div>

<a href="{{ route('email-logs.index') }}" class="btn btn-secondary mt-3">Back</a>
@endsection