@extends('layouts.app')

@section('page-title', 'Add Company Research')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2>Add Company Research</h2>
        <p>Create a new company research record</p>
    </div>
    <a href="{{ route('companies.index') }}" class="btn btn-secondary">Back to Companies</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">Please fix the highlighted fields and try again.</div>
@endif

<form method="POST" action="{{ route('companies.store') }}">
    @csrf

    <div class="card mb-4">
        <div class="card-header"><strong>Company Research Fields</strong></div>
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
                <label class="form-label">Salesforce Opportunity</label>
                <input type="text" name="salesforce_opportunity" class="form-control @error('salesforce_opportunity') is-invalid @enderror" value="{{ old('salesforce_opportunity') }}">
                @error('salesforce_opportunity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label class="form-label">Website Summary</label>
                <textarea name="website_summary" rows="5" class="form-control @error('website_summary') is-invalid @enderror">{{ old('website_summary') }}</textarea>
                @error('website_summary')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label class="form-label">Claude Prompt</label>
                <textarea name="claude_prompt" rows="6" class="form-control @error('claude_prompt') is-invalid @enderror">{{ old('claude_prompt') }}</textarea>
                @error('claude_prompt')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label class="form-label">Generated Email</label>
                <textarea name="generated_email" rows="10" class="form-control @error('generated_email') is-invalid @enderror">{{ old('generated_email') }}</textarea>
                @error('generated_email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Home URL</label>
                <input type="url" name="home_url" class="form-control @error('home_url') is-invalid @enderror" value="{{ old('home_url') }}" placeholder="https://example.com">
                @error('home_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label class="form-label">Home Content</label>
                <textarea name="home_content" rows="6" class="form-control @error('home_content') is-invalid @enderror">{{ old('home_content') }}</textarea>
                @error('home_content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">About URL</label>
                <input type="url" name="about_url" class="form-control @error('about_url') is-invalid @enderror" value="{{ old('about_url') }}" placeholder="https://example.com/about">
                @error('about_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label class="form-label">About Content</label>
                <textarea name="about_content" rows="6" class="form-control @error('about_content') is-invalid @enderror">{{ old('about_content') }}</textarea>
                @error('about_content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Career URL</label>
                <input type="url" name="career_url" class="form-control @error('career_url') is-invalid @enderror" value="{{ old('career_url') }}" placeholder="https://example.com/careers">
                @error('career_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label class="form-label">Career Content</label>
                <textarea name="career_content" rows="6" class="form-control @error('career_content') is-invalid @enderror">{{ old('career_content') }}</textarea>
                @error('career_content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">News URL</label>
                <input type="url" name="news_url" class="form-control @error('news_url') is-invalid @enderror" value="{{ old('news_url') }}" placeholder="https://example.com/news">
                @error('news_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label class="form-label">News Content</label>
                <textarea name="news_content" rows="6" class="form-control @error('news_content') is-invalid @enderror">{{ old('news_content') }}</textarea>
                @error('news_content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection