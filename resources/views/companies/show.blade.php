@extends('layouts.app')

@section('page-title', 'Company Detail')

@section('content')
<div class="page-header">
    <h2>Company Research #{{ $company->id }}</h2>
</div>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="mb-3">
    <a href="{{ route('companies.edit', $company->id) }}" class="btn btn-warning">Edit Company Research</a>
    <a href="{{ route('companies.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="card bg-white p-3">
    <p><strong>Lead:</strong> {{ $company->lead->first_name ?? '-' }} {{ $company->lead->last_name ?? '' }}</p>
    <p><strong>Salesforce Opportunity:</strong> {{ $company->salesforce_opportunity ?? '-' }}</p>
    <p><strong>Website Summary:</strong></p>
    <p>{{ $company->website_summary ?? '-' }}</p>

    <p><strong>Home URL:</strong>
        @if (!empty($company->home_url))
            <a href="{{ $company->home_url }}" target="_blank" rel="noopener noreferrer">{{ $company->home_url }}</a>
        @else
            -
        @endif
    </p>
    <p><strong>Home Content:</strong></p>
    <pre>{{ $company->home_content ?? '-' }}</pre>

    <p><strong>About URL:</strong>
        @if (!empty($company->about_url))
            <a href="{{ $company->about_url }}" target="_blank" rel="noopener noreferrer">{{ $company->about_url }}</a>
        @else
            -
        @endif
    </p>
    <p><strong>About Content:</strong></p>
    <pre>{{ $company->about_content ?? '-' }}</pre>

    <p><strong>Career URL:</strong>
        @if (!empty($company->career_url))
            <a href="{{ $company->career_url }}" target="_blank" rel="noopener noreferrer">{{ $company->career_url }}</a>
        @else
            -
        @endif
    </p>
    <p><strong>Career Content:</strong></p>
    <pre>{{ $company->career_content ?? '-' }}</pre>

    <p><strong>News URL:</strong>
        @if (!empty($company->news_url))
            <a href="{{ $company->news_url }}" target="_blank" rel="noopener noreferrer">{{ $company->news_url }}</a>
        @else
            -
        @endif
    </p>
    <p><strong>News Content:</strong></p>
    <pre>{{ $company->news_content ?? '-' }}</pre>

    <p><strong>Claude Prompt:</strong></p>
    <pre>{{ $company->claude_prompt ?? '-' }}</pre>
    <p><strong>Generated Email:</strong></p>
    <pre>{{ $company->generated_email ?? '-' }}</pre>
</div>

<a href="{{ route('companies.index') }}" class="btn btn-secondary mt-3">Back</a>
@endsection