<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Automation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #f0f2f5;
            font-family: 'Arial', sans-serif;
        }

        /* Top Bar */
        .topbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 60px;
            background: linear-gradient(135deg, #1f4e7a, #18304a);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px 0 240px;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .topbar .page-title {
            color: #ffffff99;
            font-size: 14px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .topbar .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
            font-size: 13px;
        }
        .topbar .logout-btn {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            padding: 5px 15px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
            transition: 0.2s;
        }
        .topbar .logout-btn:hover {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: 230px;
            height: 100vh;
            background: #18304a;
            z-index: 200;
            display: flex;
            flex-direction: column;
            box-shadow: 3px 0 15px rgba(0,0,0,0.2);
        }
        .sidebar .brand {
            padding: 18px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar .brand .brand-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #1f4e7a, #2d6da8);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }
        .sidebar .brand .brand-name {
            color: white;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .sidebar .brand .brand-sub {
            color: rgba(255,255,255,0.4);
            font-size: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .nav-section {
            padding: 20px 0 5px;
        }
        .nav-section-title {
            padding: 0 20px 8px;
            font-size: 10px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.25);
        }
        .nav-item a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 20px;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 13px;
            transition: 0.2s;
            border-left: 3px solid transparent;
        }
        .nav-item a:hover {
            background: rgba(255,255,255,0.05);
            color: white;
            border-left-color: #1f4e7a;
        }
        .nav-item a.active {
            background: rgba(31, 78, 122, 0.3);
            color: white;
            border-left-color: #4a9fd4;
        }
        .nav-item a i {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            margin-left: 230px;
            padding-top: 60px;
            min-height: 100vh;
        }
        .content-area {
            padding: 30px;
        }
        .page-header {
            margin-bottom: 25px;
        }
        .page-header h2 {
            font-size: 22px;
            color: #18304a;
            font-weight: 700;
        }
        .page-header p {
            color: #999;
            font-size: 13px;
            margin-top: 3px;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Top Bar -->
    <div class="topbar">
        <div class="page-title">@yield('page-title', 'Dashboard')</div>
        <div class="user-info">
            <span><i class="bi bi-person-circle"></i> {{ auth()->user()->name ?? 'Guest' }}</span>
            <form method="POST" action="{{ route('logout') }}" style="margin:0">
                @csrf
                <button type="submit" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-area">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>