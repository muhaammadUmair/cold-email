@extends('layouts.app')

@section('page-title', 'Leads')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2>Leads</h2>
        <p>All leads list</p>
    </div>
    <a href="{{ route('leads.create') }}" class="btn btn-primary">+ Add Lead</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-bordered mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Company</th>
                    <th>Website</th>
                    <th>Email</th>
                    <th>Job Title</th>
                    <th>Industry</th>
                    <th>Status</th>
                    <th>Email Sent At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leads as $lead)
                <tr>
                    <td>{{ $lead->id }}</td>
                    <td>{{ $lead->first_name }}</td>
                    <td>{{ $lead->last_name }}</td>
                    <td>{{ $lead->company }}</td>
                    <td>
                        @if($lead->website)
                            <a href="{{ $lead->website }}" target="_blank">{{ $lead->website }}</a>
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $lead->email }}</td>
                    <td>{{ $lead->job_title ?? '—' }}</td>
                    <td>{{ $lead->industry ?? '—' }}</td>
                    <td>
                        <span class="badge 
                            @if($lead->status === 'active') badge-success
                            @elseif($lead->status === 'pending') badge-warning
                            @elseif($lead->status === 'inactive') badge-danger
                            @else badge-secondary
                            @endif">
                            {{ ucfirst($lead->status ?? 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $lead->email_sent_at ? \Carbon\Carbon::parse($lead->email_sent_at)->format('d M Y, h:i A') : '—' }}</td>
                    <td>
                        <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-sm btn-info">View</a>
                        <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('leads.destroy', $lead->id) }}" method="POST" style="display:inline-block"
                            onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center">No records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($leads->hasPages())
    <div class="card-footer">
        {{ $leads->links() }}
    </div>
    @endif
</div>
@endsection