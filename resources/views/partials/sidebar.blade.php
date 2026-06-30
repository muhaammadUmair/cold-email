<div class="sidebar">
    <div class="brand">
        <div class="brand-icon">
            <i class="bi bi-lightning-charge-fill"></i>
        </div>
        <div>
            <div class="brand-name">Lead Auto</div>
            <div class="brand-sub">Management</div>
        </div>
    </div>

    <div class="nav-section">
        <div class="nav-section-title">Main Menu</div>
        <div class="nav-item">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('leads.index') }}" class="{{ request()->routeIs('leads.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Leads
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('companies.index') }}" class="{{ request()->routeIs('companies.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i> Companies
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('email-logs.index') }}" class="{{ request()->routeIs('email-logs.*') ? 'active' : '' }}">
                <i class="bi bi-envelope-fill"></i> Email Logs
            </a>
        </div>
    </div>
</div>