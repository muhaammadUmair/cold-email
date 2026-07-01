<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use App\Models\Lead;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    public function create()
    {
        $emailLog = new EmailLog();
        $leads = Lead::orderBy('first_name')->orderBy('last_name')->get();

        return view('email-logs.create', compact('emailLog', 'leads'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => ['required', 'exists:leads,id'],
            'status' => ['nullable', 'string', 'max:255'],
            'gmail_message_id' => ['nullable', 'string', 'max:255'],
            'sent_at' => ['nullable', 'date'],
        ]);

        $emailLog = EmailLog::create($validated);

        return redirect()->route('email-logs.show', $emailLog)->with('status', 'Email log created successfully.');
    }

    public function index()
    {
        $emailLogs = EmailLog::with('lead')->latest()->paginate(15);

        return view('email-logs.index', compact('emailLogs'));
    }

    public function show(string $id)
    {
        $emailLog = EmailLog::with('lead')->findOrFail($id);

        return view('email-logs.show', compact('emailLog'));
    }

    public function edit(string $id)
    {
        $emailLog = EmailLog::findOrFail($id);
        $leads = Lead::orderBy('first_name')->orderBy('last_name')->get();

        return view('email-logs.edit', compact('emailLog', 'leads'));
    }

    public function update(Request $request, string $id)
    {
        $emailLog = EmailLog::findOrFail($id);

        $validated = $request->validate([
            'lead_id' => ['required', 'exists:leads,id'],
            'status' => ['nullable', 'string', 'max:255'],
            'gmail_message_id' => ['nullable', 'string', 'max:255'],
            'sent_at' => ['nullable', 'date'],
        ]);

        $emailLog->update($validated);

        return redirect()->route('email-logs.show', $emailLog)->with('status', 'Email log updated successfully.');
    }

    public function destroy(string $id)
    {
        EmailLog::findOrFail($id)->delete();

        return redirect()->route('email-logs.index')->with('status', 'Record deleted.');
    }
}