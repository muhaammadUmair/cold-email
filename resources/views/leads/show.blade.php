@extends('layouts.app')

@section('page-title', 'Lead Detail')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2>Lead Detail</h2>
        <p>Full information of the lead</p>
    </div>
    <a href="{{ route('leads.index') }}" class="btn btn-secondary">← Back to Leads</a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ $lead->first_name }} {{ $lead->last_name }}</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped mb-0">
            <tbody>
                <tr>
                    <th style="width: 200px;">#</th>
                    <td>{{ $lead->id }}</td>
                </tr>
                <tr>
                    <th>First Name</th>
                    <td>{{ $lead->first_name ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td>{{ $lead->last_name ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Company</th>
                    <td>{{ $lead->company ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Website</th>
                    <td>
                        @if($lead->website)
                            <a href="{{ $lead->website }}" target="_blank">{{ $lead->website }}</a>
                        @else
                            —
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $lead->email ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Job Title</th>
                    <td>{{ $lead->job_title ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Industry</th>
                    <td>{{ $lead->industry ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Status</th>
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
                </tr>
                <tr>
                    <th>Email Sent At</th>
                    <td>{{ $lead->email_sent_at ? \Carbon\Carbon::parse($lead->email_sent_at)->format('d M Y, h:i A') : '—' }}</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $lead->created_at ? \Carbon\Carbon::parse($lead->created_at)->format('d M Y, h:i A') : '—' }}</td>
                </tr>
                <tr>
                    <th>Updated At</th>
                    <td>{{ $lead->updated_at ? \Carbon\Carbon::parse($lead->updated_at)->format('d M Y, h:i A') : '—' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection