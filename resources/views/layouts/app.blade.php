<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIM Coffee Shop') — Contact Coffee</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    {{-- Google Fonts: DM Sans (sebelumnya cuma ditulis di font-family tapi gak pernah di-load) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Layout styles: dipindah dari inline <style> ke resources/css/coffee-layout.css --}}
    @vite(['resources/css/coffee-layout.css'])
</head>

<script>
    document.addEventListener("DOMContentLoaded", function () {

        const theme = localStorage.getItem('theme');

        if (theme === 'light') {
            document.body.classList.add('light-mode');
        }

    });


    function toggleTheme(){

        document.body.classList.toggle('light-mode');


        if(document.body.classList.contains('light-mode')){
            localStorage.setItem('theme','light');
        }else{
            localStorage.setItem('theme','dark');
        }

    }
</script>

<script>
// Load saved theme
(function() {
    const saved = localStorage.getItem('theme');
    if (saved === 'light') {
        document.body.classList.add('light-mode');
    }
})();
</script>
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
        <a href="{{ route('owner.dashboard-stok') }}" class="{{ request()->routeIs('owner.dashboard-stok') ? 'active' : '' }}">
            <i class="fas fa-warehouse fa-fw"></i> Dashboard Stok
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

@elseif(auth()->user()->isAdmin())
    <div class="nav-item">
        <a href="{{ route('owner.dashboard-stok') }}" class="{{ request()->routeIs('owner.dashboard-stok') ? 'active' : '' }}">
            <i class="fas fa-warehouse fa-fw"></i> Dashboard Stok
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('owner.menu.index') }}" class="{{ request()->routeIs('owner.menu.*') ? 'active' : '' }}">
            <i class="fas fa-coffee fa-fw"></i> Menu
        </a>
    </div>
    <div class="nav-item">
        <a href="{{ route('owner.kategori.index') }}" class="{{ request()->routeIs('owner.kategori.*') ? 'active' : '' }}">
            <i class="fas fa-tags fa-fw"></i> Kategori
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
                <button type="submit" class="btn-logout">
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
    <div class="topbar-right d-flex align-items-center gap-3">
        <span>{{ now()->format('d M Y') }}</span>
        <span style="padding:3px 10px; border-radius:10px; background:rgba(200,169,126,0.12); color:#c8a97e; font-size:11px; font-weight:600; text-transform:uppercase;">
            {{ auth()->user()->role }}
        </span>

        {{-- Theme Toggle --}}
        <div style="display:flex; align-items:center; gap:6px;">
            <span style="font-size:11px;" id="themeIcon">🌙</span>
            <button class="theme-toggle" id="themeToggle" onclick="toggleTheme()" title="Switch Theme"></button>
            <span style="font-size:11px;">☀️</span>
        </div>
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
