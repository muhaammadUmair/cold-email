<?php

namespace App\Http\Controllers;

use App\Models\CompanyResearch;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
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

    public function destroy(string $id)
    {
        CompanyResearch::findOrFail($id)->delete();

        return redirect()->route('companies.index')->with('status', 'Record deleted.');
    }
}