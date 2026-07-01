<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leads = Lead::latest()->paginate(20);
        return view('leads.index', compact('leads'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lead = new Lead();

        return view('leads.create', compact('lead'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'company_name_for_emails' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:leads,email'],
            'email_status' => ['nullable', 'string', 'max:255'],
            'primary_email_source' => ['nullable', 'string', 'max:255'],
            'email_verification_source' => ['nullable', 'string', 'max:255'],
            'email_confidence' => ['nullable', 'string', 'max:255'],
            'email_catch_all_status' => ['nullable', 'string', 'max:255'],
            'email_last_verified_at' => ['nullable', 'date'],
            'seniority' => ['nullable', 'string', 'max:255'],
            'departments' => ['nullable', 'string', 'max:255'],
            'sub_departments' => ['nullable', 'string', 'max:255'],
            'contact_owner' => ['nullable', 'string', 'max:255'],
            'account_owner' => ['nullable', 'string', 'max:255'],
            'work_direct_phone' => ['nullable', 'string', 'max:255'],
            'home_phone' => ['nullable', 'string', 'max:255'],
            'mobile_phone' => ['nullable', 'string', 'max:255'],
            'corporate_phone' => ['nullable', 'string', 'max:255'],
            'other_phone' => ['nullable', 'string', 'max:255'],
            'do_not_call' => ['sometimes', 'boolean'],
            'stage' => ['nullable', 'string', 'max:255'],
            'lists' => ['nullable', 'string', 'max:255'],
            'last_contacted_at' => ['nullable', 'date'],
            'employees' => ['nullable', 'integer'],
            'industry' => ['nullable', 'string', 'max:255'],
            'keywords' => ['nullable', 'string'],
            'linkedin_url' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'company_linkedin_url' => ['nullable', 'string', 'max:255'],
            'facebook_url' => ['nullable', 'string', 'max:255'],
            'twitter_url' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:255'],
            'company_city' => ['nullable', 'string', 'max:255'],
            'company_state' => ['nullable', 'string', 'max:255'],
            'company_country' => ['nullable', 'string', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:255'],
            'technologies' => ['nullable', 'string'],
            'annual_revenue' => ['nullable', 'string', 'max:255'],
            'total_funding' => ['nullable', 'string', 'max:255'],
            'latest_funding' => ['nullable', 'string', 'max:255'],
            'latest_funding_amount' => ['nullable', 'string', 'max:255'],
            'last_raised_at' => ['nullable', 'string', 'max:255'],
            'subsidiary_of' => ['nullable', 'string', 'max:255'],
            'subsidiary_of_org_id' => ['nullable', 'string', 'max:255'],
            'email_sent' => ['sometimes', 'boolean'],
            'email_open' => ['sometimes', 'boolean'],
            'email_bounced' => ['sometimes', 'boolean'],
            'replied' => ['sometimes', 'boolean'],
            'demoed' => ['sometimes', 'boolean'],
            'retail_locations' => ['nullable', 'string', 'max:255'],
            'sic_codes' => ['nullable', 'string', 'max:255'],
            'naics_codes' => ['nullable', 'string', 'max:255'],
            'apollo_id' => ['nullable', 'string', 'max:255'],
            'apollo_account_id' => ['nullable', 'string', 'max:255'],
            'apollo_record_id' => ['nullable', 'string', 'max:255'],
            'secondary_email' => ['nullable', 'string', 'max:255'],
            'secondary_email_source' => ['nullable', 'string', 'max:255'],
            'secondary_email_status' => ['nullable', 'string', 'max:255'],
            'secondary_email_verification_source' => ['nullable', 'string', 'max:255'],
            'tertiary_email' => ['nullable', 'string', 'max:255'],
            'tertiary_email_source' => ['nullable', 'string', 'max:255'],
            'tertiary_email_status' => ['nullable', 'string', 'max:255'],
            'tertiary_email_verification_source' => ['nullable', 'string', 'max:255'],
            'qualify_contact' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['pending', 'sent', 'failed'])],
            'email_sent_at' => ['nullable', 'date'],
        ]);

        $validated['do_not_call'] = $request->boolean('do_not_call');
        $validated['email_sent'] = $request->boolean('email_sent');
        $validated['email_open'] = $request->boolean('email_open');
        $validated['email_bounced'] = $request->boolean('email_bounced');
        $validated['replied'] = $request->boolean('replied');
        $validated['demoed'] = $request->boolean('demoed');
        $validated['status'] = $validated['status'] ?? 'pending';

        $lead = Lead::create($validated);

        return redirect()->route('leads.show', $lead)->with('status', 'Lead created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lead = Lead::findOrFail($id);
        return view('leads.show', compact('lead'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lead = Lead::findOrFail($id);

        return view('leads.edit', compact('lead'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $lead = Lead::findOrFail($id);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'company_name_for_emails' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('leads', 'email')->ignore($lead->id)],
            'email_status' => ['nullable', 'string', 'max:255'],
            'primary_email_source' => ['nullable', 'string', 'max:255'],
            'email_verification_source' => ['nullable', 'string', 'max:255'],
            'email_confidence' => ['nullable', 'string', 'max:255'],
            'email_catch_all_status' => ['nullable', 'string', 'max:255'],
            'email_last_verified_at' => ['nullable', 'date'],
            'seniority' => ['nullable', 'string', 'max:255'],
            'departments' => ['nullable', 'string', 'max:255'],
            'sub_departments' => ['nullable', 'string', 'max:255'],
            'contact_owner' => ['nullable', 'string', 'max:255'],
            'account_owner' => ['nullable', 'string', 'max:255'],
            'work_direct_phone' => ['nullable', 'string', 'max:255'],
            'home_phone' => ['nullable', 'string', 'max:255'],
            'mobile_phone' => ['nullable', 'string', 'max:255'],
            'corporate_phone' => ['nullable', 'string', 'max:255'],
            'other_phone' => ['nullable', 'string', 'max:255'],
            'do_not_call' => ['sometimes', 'boolean'],
            'stage' => ['nullable', 'string', 'max:255'],
            'lists' => ['nullable', 'string', 'max:255'],
            'last_contacted_at' => ['nullable', 'date'],
            'employees' => ['nullable', 'integer'],
            'industry' => ['nullable', 'string', 'max:255'],
            'keywords' => ['nullable', 'string'],
            'linkedin_url' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'company_linkedin_url' => ['nullable', 'string', 'max:255'],
            'facebook_url' => ['nullable', 'string', 'max:255'],
            'twitter_url' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:255'],
            'company_city' => ['nullable', 'string', 'max:255'],
            'company_state' => ['nullable', 'string', 'max:255'],
            'company_country' => ['nullable', 'string', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:255'],
            'technologies' => ['nullable', 'string'],
            'annual_revenue' => ['nullable', 'string', 'max:255'],
            'total_funding' => ['nullable', 'string', 'max:255'],
            'latest_funding' => ['nullable', 'string', 'max:255'],
            'latest_funding_amount' => ['nullable', 'string', 'max:255'],
            'last_raised_at' => ['nullable', 'string', 'max:255'],
            'subsidiary_of' => ['nullable', 'string', 'max:255'],
            'subsidiary_of_org_id' => ['nullable', 'string', 'max:255'],
            'email_sent' => ['sometimes', 'boolean'],
            'email_open' => ['sometimes', 'boolean'],
            'email_bounced' => ['sometimes', 'boolean'],
            'replied' => ['sometimes', 'boolean'],
            'demoed' => ['sometimes', 'boolean'],
            'retail_locations' => ['nullable', 'string', 'max:255'],
            'sic_codes' => ['nullable', 'string', 'max:255'],
            'naics_codes' => ['nullable', 'string', 'max:255'],
            'apollo_id' => ['nullable', 'string', 'max:255'],
            'apollo_account_id' => ['nullable', 'string', 'max:255'],
            'apollo_record_id' => ['nullable', 'string', 'max:255'],
            'secondary_email' => ['nullable', 'string', 'max:255'],
            'secondary_email_source' => ['nullable', 'string', 'max:255'],
            'secondary_email_status' => ['nullable', 'string', 'max:255'],
            'secondary_email_verification_source' => ['nullable', 'string', 'max:255'],
            'tertiary_email' => ['nullable', 'string', 'max:255'],
            'tertiary_email_source' => ['nullable', 'string', 'max:255'],
            'tertiary_email_status' => ['nullable', 'string', 'max:255'],
            'tertiary_email_verification_source' => ['nullable', 'string', 'max:255'],
            'qualify_contact' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['pending', 'sent', 'failed'])],
            'email_sent_at' => ['nullable', 'date'],
        ]);

        $validated['do_not_call'] = $request->boolean('do_not_call');
        $validated['email_sent'] = $request->boolean('email_sent');
        $validated['email_open'] = $request->boolean('email_open');
        $validated['email_bounced'] = $request->boolean('email_bounced');
        $validated['replied'] = $request->boolean('replied');
        $validated['demoed'] = $request->boolean('demoed');

        $lead->update($validated);

        return redirect()->route('leads.show', $lead)->with('status', 'Lead updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}