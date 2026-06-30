@extends('layouts.app')

@section('page-title', 'Companies')

@section('content')
<div class="page-header">
    <h2>Companies</h2>
    <p>Company research records</p>
</div>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<table class="table table-bordered bg-white">
    <thead>
        <tr>
            <th>#</th>
            <th>Lead</th>
            <th>Website Summary</th>
            <th>Salesforce Opportunity</th>
            <th>Created</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($companies as $company)
            <tr>
                <td>{{ $company->id }}</td>
                <td>{{ $company->lead->first_name ?? '-' }} {{ $company->lead->last_name ?? '' }}</td>
                <td>{{ Str::limit($company->website_summary, 60) }}</td>
                <td>{{ $company->salesforce_opportunity ?? '-' }}</td>
                <td>{{ $company->created_at->format('d M Y') }}</td>
                <td>
                    <a href="{{ route('companies.show', $company->id) }}" class="btn btn-sm btn-primary">View</a>
                    <form action="{{ route('companies.destroy', $company->id) }}" method="POST" style="display:inline">
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

{{ $companies->links() }}
@endsection