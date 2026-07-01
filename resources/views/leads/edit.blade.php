@extends('layouts.app')

@section('page-title', 'Edit Lead')

@section('content')
@php
    $dateTimeValue = function ($value) {
        return $value ? \Illuminate\Support\Carbon::parse($value)->format('Y-m-d\TH:i') : '';
    };

    $textFields = [
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'title' => 'Title',
        'company' => 'Company',
        'company_name_for_emails' => 'Company Name For Emails',
        'email' => 'Email',
        'email_status' => 'Email Status',
        'primary_email_source' => 'Primary Email Source',
        'email_verification_source' => 'Email Verification Source',
        'email_confidence' => 'Email Confidence',
        'email_catch_all_status' => 'Email Catch All Status',
        'seniority' => 'Seniority',
        'departments' => 'Departments',
        'sub_departments' => 'Sub Departments',
        'contact_owner' => 'Contact Owner',
        'account_owner' => 'Account Owner',
        'work_direct_phone' => 'Work Direct Phone',
        'home_phone' => 'Home Phone',
        'mobile_phone' => 'Mobile Phone',
        'corporate_phone' => 'Corporate Phone',
        'other_phone' => 'Other Phone',
        'stage' => 'Stage',
        'lists' => 'Lists',
        'industry' => 'Industry',
        'linkedin_url' => 'LinkedIn URL',
        'website' => 'Website',
        'company_linkedin_url' => 'Company LinkedIn URL',
        'facebook_url' => 'Facebook URL',
        'twitter_url' => 'Twitter URL',
        'city' => 'City',
        'state' => 'State',
        'country' => 'Country',
        'company_address' => 'Company Address',
        'company_city' => 'Company City',
        'company_state' => 'Company State',
        'company_country' => 'Company Country',
        'company_phone' => 'Company Phone',
        'annual_revenue' => 'Annual Revenue',
        'total_funding' => 'Total Funding',
        'latest_funding' => 'Latest Funding',
        'latest_funding_amount' => 'Latest Funding Amount',
        'last_raised_at' => 'Last Raised At',
        'subsidiary_of' => 'Subsidiary Of',
        'subsidiary_of_org_id' => 'Subsidiary Of Org ID',
        'retail_locations' => 'Retail Locations',
        'sic_codes' => 'SIC Codes',
        'naics_codes' => 'NAICS Codes',
        'apollo_id' => 'Apollo ID',
        'apollo_account_id' => 'Apollo Account ID',
        'apollo_record_id' => 'Apollo Record ID',
        'secondary_email' => 'Secondary Email',
        'secondary_email_source' => 'Secondary Email Source',
        'secondary_email_status' => 'Secondary Email Status',
        'secondary_email_verification_source' => 'Secondary Email Verification Source',
        'tertiary_email' => 'Tertiary Email',
        'tertiary_email_source' => 'Tertiary Email Source',
        'tertiary_email_status' => 'Tertiary Email Status',
        'tertiary_email_verification_source' => 'Tertiary Email Verification Source',
        'qualify_contact' => 'Qualify Contact',
    ];

    $dateFields = [
        'email_last_verified_at' => 'Email Last Verified At',
        'last_contacted_at' => 'Last Contacted At',
        'email_sent_at' => 'Email Sent At',
    ];

    $textareaFields = [
        'keywords' => 'Keywords',
        'technologies' => 'Technologies',
    ];

    $booleanFields = [
        'do_not_call' => 'Do Not Call',
        'email_sent' => 'Email Sent',
        'email_open' => 'Email Open',
        'email_bounced' => 'Email Bounced',
        'replied' => 'Replied',
        'demoed' => 'Demoed',
    ];
@endphp

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2>Edit Lead</h2>
        <p>Update all lead fields</p>
    </div>
    <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-secondary">Back to Lead</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        Please fix the highlighted fields and try again.
    </div>
@endif

<form method="POST" action="{{ route('leads.update', $lead->id) }}">
    @csrf
    @method('PUT')

    <div class="card mb-4">
        <div class="card-header"><strong>Record Info</strong></div>
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label class="form-label">ID</label>
                <input type="text" class="form-control" value="{{ $lead->id }}" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Created At</label>
                <input type="text" class="form-control" value="{{ $lead->created_at }}" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Updated At</label>
                <input type="text" class="form-control" value="{{ $lead->updated_at }}" readonly>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><strong>Core Details</strong></div>
        <div class="card-body row g-3">
            @foreach ($textFields as $field => $label)
                <div class="col-md-6">
                    <label class="form-label">{{ $label }}</label>
                    <input
                        type="text"
                        name="{{ $field }}"
                        class="form-control @error($field) is-invalid @enderror"
                        value="{{ old($field, $lead->{$field}) }}"
                    >
                    @error($field)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endforeach

            <div class="col-md-6">
                <label class="form-label">Employees</label>
                <input
                    type="number"
                    name="employees"
                    class="form-control @error('employees') is-invalid @enderror"
                    value="{{ old('employees', $lead->employees) }}"
                >
                @error('employees')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><strong>Dates and Text Areas</strong></div>
        <div class="card-body row g-3">
            @foreach ($dateFields as $field => $label)
                <div class="col-md-4">
                    <label class="form-label">{{ $label }}</label>
                    <input
                        type="datetime-local"
                        name="{{ $field }}"
                        class="form-control @error($field) is-invalid @enderror"
                        value="{{ $dateTimeValue(old($field, $lead->{$field})) }}"
                    >
                    @error($field)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endforeach

            @foreach ($textareaFields as $field => $label)
                <div class="col-12">
                    <label class="form-label">{{ $label }}</label>
                    <textarea
                        name="{{ $field }}"
                        rows="4"
                        class="form-control @error($field) is-invalid @enderror"
                    >{{ old($field, $lead->{$field}) }}</textarea>
                    @error($field)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endforeach
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><strong>Boolean Flags</strong></div>
        <div class="card-body row g-3">
            @foreach ($booleanFields as $field => $label)
                <div class="col-md-4">
                    <div class="form-check mt-4">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="{{ $field }}"
                            value="1"
                            id="{{ $field }}"
                            @checked(old($field, $lead->{$field}))
                        >
                        <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                    </div>
                    @error($field)
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            @endforeach
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection