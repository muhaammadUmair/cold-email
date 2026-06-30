<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
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

    public function destroy(string $id)
    {
        EmailLog::findOrFail($id)->delete();

        return redirect()->route('email-logs.index')->with('status', 'Record deleted.');
    }
}