@extends('layouts.app')

@section('page-title', 'Email Logs')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2>Email Logs</h2>
        <p>Sent email tracking</p>
    </div>
    <a href="{{ route('email-logs.create') }}" class="btn btn-primary">+ Add Email Log</a>
</div>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<table class="table table-bordered bg-white">
    <thead>
        <tr>
            <th>#</th>
            <th>Lead</th>
            <th>Status</th>
            <th>Gmail Message ID</th>
            <th>Sent At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($emailLogs as $log)
            <tr>
                <td>{{ $log->id }}</td>
                <td>{{ $log->lead->first_name ?? '-' }} {{ $log->lead->last_name ?? '' }}</td>
                <td>{{ $log->status ?? '-' }}</td>
                <td>{{ $log->gmail_message_id ?? '-' }}</td>
                <td>{{ $log->sent_at ? $log->sent_at->format('d M Y H:i') : '-' }}</td>
                <td>
                    <a href="{{ route('email-logs.show', $log->id) }}" class="btn btn-sm btn-primary">View</a>
                    <form action="{{ route('email-logs.destroy', $log->id) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center">No records found.</td></tr>
        @endforelse
    </tbody>
</table>

{{ $emailLogs->links() }}
@endsection