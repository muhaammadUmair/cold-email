<?php

namespace App\Http\Controllers;

use App\Models\CompanyResearch;
use App\Models\Lead;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function create()
    {
        $company = new CompanyResearch();
        $leads = Lead::orderBy('first_name')->orderBy('last_name')->get();

        return view('companies.create', compact('company', 'leads'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => ['required', 'exists:leads,id'],
            'website_summary' => ['nullable', 'string'],
            'salesforce_opportunity' => ['nullable', 'string', 'max:255'],
            'claude_prompt' => ['nullable', 'string'],
            'generated_email' => ['nullable', 'string'],
        ]);

        $company = CompanyResearch::create($validated);

        return redirect()->route('companies.show', $company)->with('status', 'Company research created successfully.');
    }

    public function index()
    {
        $companies = CompanyResearch::with('lead')->latest()->paginate(15);

        return view('companies.index', compact('companies'));
    }

    public function show(string $id)
    {
        $company = CompanyResearch::with('lead')->findOrFail($id);

        return view('companies.show', compact('company'));
    }

    public function edit(string $id)
    {
        $company = CompanyResearch::findOrFail($id);
        $leads = Lead::orderBy('first_name')->orderBy('last_name')->get();

        return view('companies.edit', compact('company', 'leads'));
    }

    public function update(Request $request, string $id)
    {
        $company = CompanyResearch::findOrFail($id);

        $validated = $request->validate([
            'lead_id' => ['required', 'exists:leads,id'],
            'website_summary' => ['nullable', 'string'],
            'salesforce_opportunity' => ['nullable', 'string', 'max:255'],
            'claude_prompt' => ['nullable', 'string'],
            'generated_email' => ['nullable', 'string'],
        ]);

        $company->update($validated);

        return redirect()->route('companies.show', $company)->with('status', 'Company research updated successfully.');
    }

    public function destroy(string $id)
    {
        CompanyResearch::findOrFail($id)->delete();

        return redirect()->route('companies.index')->with('status', 'Record deleted.');
    }
}