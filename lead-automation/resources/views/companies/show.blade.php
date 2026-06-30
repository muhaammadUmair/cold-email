@extends('layouts.app')

@section('page-title', 'Company Detail')

@section('content')
<div class="page-header">
    <h2>Company Research #{{ $company->id }}</h2>
</div>

<div class="card bg-white p-3">
    <p><strong>Lead:</strong> {{ $company->lead->first_name ?? '-' }} {{ $company->lead->last_name ?? '' }}</p>
    <p><strong>Salesforce Opportunity:</strong> {{ $company->salesforce_opportunity ?? '-' }}</p>
    <p><strong>Website Summary:</strong></p>
    <p>{{ $company->website_summary ?? '-' }}</p>
    <p><strong>Claude Prompt:</strong></p>
    <pre>{{ $company->claude_prompt ?? '-' }}</pre>
    <p><strong>Generated Email:</strong></p>
    <pre>{{ $company->generated_email ?? '-' }}</pre>
</div>

<a href="{{ route('companies.index') }}" class="btn btn-secondary mt-3">Back</a>
@endsection