@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="page-header">
    <h2>Dashboard</h2>
    <p>Welcome to Lead Automation System</p>
</div>

<div class="row g-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted" style="font-size:12px; letter-spacing:1px;">TOTAL LEADS</div>
            <div style="font-size:32px; font-weight:bold; color:#1f4e7a;">0</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted" style="font-size:12px; letter-spacing:1px;">EMAILS SENT</div>
            <div style="font-size:32px; font-weight:bold; color:#1f4e7a;">0</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted" style="font-size:12px; letter-spacing:1px;">PENDING</div>
            <div style="font-size:32px; font-weight:bold; color:#1f4e7a;">0</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="text-muted" style="font-size:12px; letter-spacing:1px;">FAILED</div>
            <div style="font-size:32px; font-weight:bold; color:#1f4e7a;">0</div>
        </div>
    </div>
</div>
@endsection