<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIM Coffee Shop') — Contact Coffee</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --bg-main: #0f1117;
            --bg-card: #1a1d27;
            --bg-sidebar: #161920;
            --border: #23262f;
            --gold: #c8a97e;
            --text: #e8e6e0;
            --muted: #888;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            background: var(--bg-main);
            color: var(--text);
            font-family: 'DM Sans', 'Segoe UI', sans-serif;
        }

        /* ── LAYOUT WRAPPER ── */
        .layout-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 220px;
            min-height: 100vh;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            overflow-y: auto;
        }

        .sidebar-logo {
            padding: 18px 16px;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }

        .sidebar-logo .brand {
            color: var(--gold);
            font-weight: 700;
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            line-height: 1.3;
        }

        .sidebar-logo .sub {
            color: #555;
            font-size: 10px;
            margin-top: 2px;
        }

        .sidebar nav {
            flex: 1;
            padding: 8px 0;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 16px;
            color: var(--muted);
            text-decoration: none;
            font-size: 13px;
            border-left: 3px solid transparent;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .nav-item a:hover,
        .nav-item a.active {
            color: var(--gold);
            background: rgba(200,169,126,0.08);
            border-left-color: var(--gold);
        }

        .user-info {
            padding: 12px 14px;
            border-top: 1px solid var(--border);
            flex-shrink: 0;
        }

        .user-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, #c8a97e, #a87d50);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: #1a1208;
            flex-shrink: 0;
        }

        /* ── MAIN CONTENT ── */
        .main-content {
            margin-left: 220px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── TOPBAR ── */
        .topbar {
            height: 52px;
            background: var(--bg-sidebar);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 50;
            flex-shrink: 0;
        }

        .topbar-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 12px;
            color: var(--muted);
        }

        /* ── CONTENT BODY ── */
        .content-body {
            padding: 24px;
            flex: 1;
        }

        /* ── CARDS ── */
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px 20px;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .stat-card .accent-bar {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            border-radius: 12px 12px 0 0;
        }

        .stat-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 22px;
            font-weight: 700;
            line-height: 1;
            color: var(--text);
        }

        .stat-sub {
            font-size: 11px;
            color: #555;
            margin-top: 6px;
        }

        .chart-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px 20px;
        }

        .chart-title {
            font-size: 12px;
            font-weight: 600;
            color: var(--gold);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 16px;
        }

        .alert-row {
            background: #1f1a14;
            border: 1px solid #3a2a14;
            border-radius: 8px;
            padding: 8px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* ── FORM CONTROLS ── */
        .form-control,
        .form-select {
            background: #0f1117 !important;
            border: 1px solid #2a2d38 !important;
            color: #e8e6e0 !important;
            border-radius: 8px !important;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #c8a97e !important;
            box-shadow: 0 0 0 3px rgba(200,169,126,0.1) !important;
            color: #e8e6e0 !important;
            background: #0f1117 !important;
        }

        .form-control::placeholder { color: #555 !important; }

        /* ── BUTTONS ── */
        .btn-gold {
            background: linear-gradient(135deg, #c8a97e, #a87d50);
            border: none;
            color: #1a1208;
            font-weight: 700;
            border-radius: 8px;
            padding: 8px 20px;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-gold:hover {
            background: linear-gradient(135deg, #d4b88a, #b88d60);
            color: #1a1208;
        }

        /* ── TABLE ── */
        .table-dark-custom {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .table-dark-custom th {
            padding: 8px 10px;
            border-bottom: 1px solid #23262f;
            color: #555;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-weight: 600;
            white-space: nowrap;
        }

        .table-dark-custom td {
            padding: 10px;
            border-bottom: 1px solid #1e2130;
            vertical-align: middle;
        }

        .table-dark-custom tbody tr:hover td {
            background: rgba(255,255,255,0.02);
        }

        /* ── BADGES ── */
        .badge-aktif {
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 600;
            background: rgba(62,207,142,0.12);
            color: #3ecf8e;
        }

        .badge-nonaktif {
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 600;
            background: rgba(224,124,58,0.12);
            color: #e07c3a;
        }

        /* ── ALERTS ── */
        .alert-success-custom {
            background: #0f2a1a;
            border: 1px solid #1a5c30;
            color: #3ecf8e;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            margin: 12px 24px 0;
        }

        .alert-error-custom {
            background: #2a1414;
            border: 1px solid #5a2020;
            color: #e07c7c;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            margin: 12px 24px 0;
        }

        /* ── PAGINATION ── */
        .pagination .page-link {
            background: var(--bg-card);
            border-color: var(--border);
            color: var(--muted);
        }
        .pagination .page-item.active .page-link {
            background: var(--gold);
            border-color: var(--gold);
            color: #1a1208;
        }
        .pagination .page-link:hover {
            background: #23262f;
            color: var(--gold);
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="layout-wrapper">

    {{-- ── SIDEBAR ── --}}
    <div class="sidebar">
        <div class="sidebar-logo">
            <div class="d-flex align-items-center gap-2">
                {{-- Logo --}}
                <img src="{{ asset('images/logo.png') }}"
                     alt="Logo"
                     style="width:36px; height:36px; border-radius:50%; object-fit:cover; flex-shrink:0;"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div style="display:none; width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,#c8a97e,#a87d50); align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">☕</div>
                <div>
                    <div class="brand">CONTACT COFFEE</div>
                    <div class="sub">SIM Coffee Shop v1.0</div>
                </div>
            </div>
        </div>

        <nav>
            @if(auth()->user()->isOwner())
                <div class="nav-item">
                    <a href="{{ route('owner.dashboard') }}" class="{{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-line fa-fw"></i> Dashboard
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('owner.users.index') }}" class="{{ request()->routeIs('owner.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users fa-fw"></i> Users
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('owner.kategori.index') }}" class="{{ request()->routeIs('owner.kategori.*') ? 'active' : '' }}">
                        <i class="fas fa-tags fa-fw"></i> Kategori
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('owner.menu.index') }}" class="{{ request()->routeIs('owner.menu.*') ? 'active' : '' }}">
                        <i class="fas fa-coffee fa-fw"></i> Menu
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('owner.bahan-baku.index') }}" class="{{ request()->routeIs('owner.bahan-baku.*') ? 'active' : '' }}">
                        <i class="fas fa-box fa-fw"></i> Bahan Baku
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('owner.resep.index') }}" class="{{ request()->routeIs('owner.resep.*') ? 'active' : '' }}">
                        <i class="fas fa-scroll fa-fw"></i> Resep (BOM)
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('owner.riwayat-stok.index') }}" class="{{ request()->routeIs('owner.riwayat-stok.*') ? 'active' : '' }}">
                        <i class="fas fa-history fa-fw"></i> Riwayat Stok
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('owner.laporan.index') }}" class="{{ request()->routeIs('owner.laporan.index') ? 'active' : '' }}">
                        <i class="fas fa-file-alt fa-fw"></i> Laporan
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('owner.laporan.bulanan') }}" class="{{ request()->routeIs('owner.laporan.bulanan') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt fa-fw"></i> Laporan Bulanan
                    </a>
                </div>
            @else
                <div class="nav-item">
                    <a href="{{ route('kasir.pos.index') }}" class="{{ request()->routeIs('kasir.pos.*') ? 'active' : '' }}">
                        <i class="fas fa-cash-register fa-fw"></i> POS
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('kasir.transaksi.index') }}" class="{{ request()->routeIs('kasir.transaksi.*') ? 'active' : '' }}">
                        <i class="fas fa-receipt fa-fw"></i> Riwayat Transaksi
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('kasir.laporan.index') }}" class="{{ request()->routeIs('kasir.laporan.*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt fa-fw"></i> Laporan
                    </a>
                </div>
            @endif
        </nav>

        <div class="user-info">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div style="overflow:hidden;">
                    <div style="font-size:11px; font-weight:600; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ auth()->user()->name }}
                    </div>
                    <div style="font-size:10px; color:#555;">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        style="width:100%; padding:6px; border-radius:6px; border:1px solid #2a2d38; background:transparent; color:#666; font-size:11px; cursor:pointer; transition:all 0.15s;"
                        onmouseover="this.style.color='#e07c7c'; this.style.borderColor='#5a2020';"
                        onmouseout="this.style.color='#666'; this.style.borderColor='#2a2d38';">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>

    {{-- ── MAIN ── --}}
    <div class="main-content">

        {{-- Topbar --}}
        <div class="topbar">
            <div class="topbar-title">@yield('page-title')</div>
            <div class="topbar-right">
                <span>{{ now()->format('d M Y') }}</span>
                <span style="padding:3px 10px; border-radius:10px; background:rgba(200,169,126,0.12); color:#c8a97e; font-size:11px; font-weight:600; text-transform:uppercase;">
                    {{ auth()->user()->role }}
                </span>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert-success-custom">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert-error-custom">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            </div>
        @endif

        {{-- Content --}}
        <div class="content-body">
            @yield('content')
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>