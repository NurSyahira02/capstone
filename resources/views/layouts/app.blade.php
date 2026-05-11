<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>NinjaVault System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/jqvmap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/jquery.vmap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/10bestdesign/jqvmap@master/dist/maps/jquery.vmap.malaysia.js"></script>

    <style>
        body { background:#f6f7fb; min-height: 100vh; font-family: 'Inter', sans-serif; }
        .sidebar {
            width: 260px;
            height: 100vh;
            background: #ffffff;
            position: fixed;
            border-right: 1px solid #e0e0e0;
            padding: 20px;
            z-index: 1000;
        }
        .main-content { margin-left: 260px; padding: 30px; }
        .nav-link { color: #555; padding: 12px 15px; border-radius: 8px; margin-bottom: 8px; font-weight: 500; text-decoration: none; display: block; }
        .nav-link:hover { background: #fff5f5; color: #dc3545; }
        .nav-link.active { background: #dc3545; color: #fff !important; box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3); }
        .sidebar-brand { font-size: 1.6rem; font-weight: 800; color: #dc3545; text-decoration: none; display: block; margin-bottom: 40px; }
        
        /* Map Styling to match NinjaVault theme */
        .jqvmap-zoomin, .jqvmap-zoomout { width: 15px; height: 15px; background: #dc3545; }
    </style>
</head>
<body>

<div class="sidebar shadow-sm">
    <a href="/" class="sidebar-brand text-center">
        <i class="bi bi-shield-lock-fill me-2"></i>NinjaVault
    </a>
    <nav class="nav flex-column">
        <a class="nav-link {{ request()->is('/') || request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="bi bi-grid-1x2-fill me-2"></i> MAIN PAGE
        </a>
        <a class="nav-link {{ request()->is('feedback') ? 'active' : '' }}" href="{{ route('feedback') }}">
            <i class="bi bi-people-fill me-2"></i> CUSTOMER
        </a>
        <a class="nav-link text-muted" href="#"><i class="bi bi-chat-dots me-2"></i> CHATBOT</a>
        <a class="nav-link text-muted" href="#"><i class="bi bi-lightning-charge me-2"></i> FLASH</a>
    </nav>
</div>

<div class="main-content">
    @yield('content')
</div>

</body>
</html>