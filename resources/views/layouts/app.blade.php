<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiniStack — @yield('title', 'Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Syne:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="font-syne bg-base text-ink-primary flex min-h-screen overflow-x-hidden" style="overscroll-behavior-y:none">

    {{-- Sidebar overlay (mobile) --}}
    <div id="sidebarOverlay" class="hidden fixed inset-0 bg-black/60 z-[99]" onclick="toggleSidebar()"></div>

    {{-- ── Sidebar ─────────────────────────────────────────── --}}
    <aside class="sidebar" id="sidebar">

        {{-- Brand --}}
        <div class="flex items-center gap-2.5 px-5 py-5 border-b border-rim">
            <div
                class="w-9 h-9 bg-grad-accent-diag rounded-lg flex items-center justify-center text-[17px] shadow-brand flex-shrink-0">
                ☁</div>
            <div class="text-[18px] font-extrabold text-ink-primary">Mini<span class="text-accent-cyan">Stack</span>
            </div>
        </div>

        {{-- User block --}}
        <div class="flex items-center gap-2.5 mx-3 my-3 bg-white/[0.02] border border-rim rounded-[10px] px-4 py-3.5">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-[13px] font-bold text-white flex-shrink-0"
                style="background: linear-gradient(135deg, #1a6cf6, #a78bfa)">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
            </div>
            <div class="overflow-hidden">
                <div class="text-[13px] font-bold text-ink-primary whitespace-nowrap overflow-hidden text-ellipsis">
                    {{ auth()->user()->name ?? 'User' }}
                </div>
                <div class="font-space text-[9px] text-accent-green tracking-[0.5px] uppercase">
                    @php $activePlan = auth()->user()->storageSubscriptions()->where('is_active', true)->latest()->value('plan') ?? 'free'; @endphp
                    {{ ucfirst($activePlan) }} Plan
                </div>
            </div>
            <div
                class="w-[7px] h-[7px] bg-accent-green rounded-full ml-auto flex-shrink-0 shadow-glow-green animate-pulse-dot">
            </div>
        </div>

        {{-- Storage mini-widget --}}
        <div class="mx-3 mb-1 bg-white/[0.02] border border-rim rounded-[10px] px-3.5 py-3.5">
            <div class="flex items-center justify-between text-[11px] text-ink-muted mb-2">
                <span>Storage</span>
                <strong class="font-space text-accent-bright">{{ auth()->user()->storage_used_gb ?? '0' }} GB</strong>
            </div>
            <div class="bg-rim rounded h-[5px] overflow-hidden">
                <div class="h-full bg-gradient-to-r from-accent to-accent-cyan rounded transition-all duration-500"
                    style="width: {{ auth()->user()->storage_used_percentage ?? 0 }}%"></div>
            </div>
            <div class="font-space text-[9px] text-ink-dim mt-1.5">
                dari {{ auth()->user()->storage_quota_gb ?? 5 }} GB kuota
            </div>
        </div>

        {{-- Main nav --}}
        <nav class="px-3 mt-1.5">
            <div class="font-space text-[9px] text-ink-dim tracking-[2px] uppercase px-2 py-2.5">Utama</div>

            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="w-5 text-center text-[15px]">⬡</span>
                Dashboard
            </a>

            <a href="{{ route('storage.index') }}"
                class="nav-item {{ request()->routeIs('storage.*') ? 'active' : '' }}">
                <span class="w-5 text-center text-[15px]">🗄</span>
                Storage
            </a>

            <a href="{{ route('compute.index') }}"
                class="nav-item {{ request()->routeIs('compute.*') ? 'active' : '' }}">
                <span class="w-5 text-center text-[15px]">⚙</span>
                Compute
            </a>

            <a href="{{ route('database.index') }}"
                class="nav-item {{ request()->routeIs('database.*') ? 'active' : '' }}">
                <span class="w-5 text-center text-[15px]">🗃</span>
                Database
            </a>

            <a href="{{ route('credentials.index') }}"
                class="nav-item {{ request()->routeIs('credentials.*') ? 'active' : '' }}">
                <span class="w-5 text-center text-[15px]">🔑</span>
                Access Keys
            </a>
        </nav>

        {{-- Services nav --}}
        <nav class="px-3 mt-1">
            <div class="font-space text-[9px] text-ink-dim tracking-[2px] uppercase px-2 py-2.5">Layanan</div>

            <a href="{{ route('subscriptions.index') }}"
                class="nav-item {{ request()->routeIs('subscriptions.*') ? 'active' : '' }}">
                <span class="w-5 text-center text-[15px]">📦</span>
                Paket Langganan
            </a>

            <a href="{{ route('activity.index') }}"
                class="nav-item {{ request()->routeIs('activity.*') ? 'active' : '' }}">
                <span class="w-5 text-center text-[15px]">📋</span>
                Log Aktivitas
            </a>

            <a href="{{ route('billing.index') }}"
                class="nav-item {{ request()->routeIs('billing.*') ? 'active' : '' }}">
                <span class="w-5 text-center text-[15px]">💳</span>
                Billing
            </a>
        </nav>

        {{-- Account nav --}}
        <nav class="px-3 mt-1">
            <div class="font-space text-[9px] text-ink-dim tracking-[2px] uppercase px-2 py-2.5">Akun</div>

            <a href="{{ route('profile.edit') }}"
                class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <span class="w-5 text-center text-[15px]">👤</span>
                Profil Saya
            </a>

            <a href="{{ route('settings.index') }}"
                class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <span class="w-5 text-center text-[15px]">⚙</span>
                Pengaturan
            </a>
        </nav>

        {{-- Logout --}}
        <div class="mt-auto px-3 py-3 border-t border-rim">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-item hover:text-accent-red hover:bg-accent-red/[0.06]">
                    <span class="w-5 text-center text-[15px]">→</span>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Topbar ───────────────────────────────────────────── --}}
    <header
        class="fixed top-0 right-0 left-0 lg:left-sidebar h-topbar bg-base/95 border-b border-rim backdrop-blur-md flex items-center px-7 gap-4 z-[90]"
        style="overscroll-behavior:contain">

        {{-- Mobile toggle --}}
        <button onclick="toggleSidebar()"
            class="lg:hidden bg-card border border-rim rounded-lg w-[34px] h-[34px] flex items-center justify-center text-[18px] text-ink-primary cursor-pointer">
            ☰
        </button>

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-[13px] text-ink-muted">
            <span>MiniStack</span>
            <span class="text-ink-dim">/</span>
            <span class="text-ink-primary font-bold">@yield('title', 'Dashboard')</span>
        </div>

        {{-- Right side --}}
        <div class="ml-auto flex items-center gap-3.5">
            <div
                class="flex items-center gap-1.5 bg-card border border-rim rounded-lg px-3 py-[5px] font-space text-[10px] text-ink-muted">
                <div class="w-1.5 h-1.5 rounded-full bg-accent-green shadow-glow-green"></div>
                ID-JKT1
            </div>

            <a href="{{ route('activity.index') }}"
                class="w-[34px] h-[34px] bg-card border border-rim rounded-lg flex items-center justify-center text-[15px] text-ink-muted no-underline transition-all hover:border-rim-light hover:text-ink-primary">
                📋
            </a>

            <a href="#"
                class="w-[34px] h-[34px] bg-card border border-rim rounded-lg flex items-center justify-center text-[15px] text-ink-muted no-underline transition-all hover:border-rim-light hover:text-ink-primary relative">
                🔔
                <span
                    class="absolute top-1.5 right-[7px] w-1.5 h-1.5 bg-accent-red rounded-full border-[1.5px] border-base"></span>
            </a>

            <a href="{{ route('profile.edit') }}"
                class="w-[34px] h-[34px] bg-card border border-rim rounded-lg flex items-center justify-center text-[15px] text-ink-muted no-underline transition-all hover:border-rim-light hover:text-ink-primary">
                👤
            </a>
        </div>
    </header>

    {{-- ── Main content ─────────────────────────────────────── --}}
    <main class="ml-0 lg:ml-sidebar mt-topbar flex-1 p-7 min-h-[calc(100vh-60px)]">

        @if (session('success'))
            <div class="alert alert-success">✓ {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">✕ {{ session('error') }}</div>
        @endif
        @if (session('info'))
            <div class="alert alert-info">ℹ {{ session('info') }}</div>
        @endif

        @yield('content')
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('!block');
            overlay.classList.toggle('hidden');
        }
    </script>
    @stack('scripts')
</body>

</html>
