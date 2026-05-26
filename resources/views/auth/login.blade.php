<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiniStack — Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Syne:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body class="font-syne bg-deep text-ink-primary min-h-screen flex items-center justify-center overflow-hidden relative">

    {{-- Animated grid background --}}
    <div class="bg-grid-anim"></div>

    {{-- Orbs --}}
    <div class="fixed w-[500px] h-[500px] rounded-full pointer-events-none blur-[80px] top-[-150px] left-[-150px] animate-orb-float"
        style="background: rgba(26,108,246,0.12)"></div>
    <div class="fixed w-[400px] h-[400px] rounded-full pointer-events-none blur-[80px] bottom-[-100px] right-[-100px] animate-orb-float-rev"
        style="background: rgba(0,212,255,0.07)"></div>

    {{-- Scanlines --}}
    <div class="scanlines-overlay"></div>

    {{-- Login card --}}
    <div class="relative z-10 flex w-[min(920px,96vw)] min-h-[540px] rounded-[20px] overflow-hidden border border-rim animate-card-in"
        style="box-shadow: 0 0 0 1px rgba(26,108,246,0.10), 0 40px 80px rgba(0,0,0,0.60), inset 0 1px 0 rgba(255,255,255,0.04)">

        {{-- ── Left panel ──────────────────────────────────── --}}
        <div class="flex-1 hidden md:flex flex-col justify-between px-10 py-12 relative overflow-hidden"
            style="background: linear-gradient(135deg, #0b1628 0%, #0a1932 60%, #091525 100%)">
            {{-- Glow orbs --}}
            <div class="absolute top-[-80px] right-[-80px] w-[260px] h-[260px] rounded-full pointer-events-none"
                style="background: radial-gradient(circle, rgba(26,108,246,0.18) 0%, transparent 70%)"></div>
            <div class="absolute bottom-[-60px] left-[-60px] w-[200px] h-[200px] rounded-full pointer-events-none"
                style="background: radial-gradient(circle, rgba(0,212,255,0.10) 0%, transparent 70%)"></div>

            {{-- Brand --}}
            <div class="flex items-center gap-3 relative z-[1]">
                <div class="w-[42px] h-[42px] bg-grad-accent-diag rounded-[10px] flex items-center justify-center text-[20px]"
                    style="box-shadow: 0 0 20px rgba(26,108,246,0.4)">☁</div>
                <div class="text-[22px] font-extrabold tracking-[-0.5px] text-ink-primary">
                    Mini<span class="text-accent-cyan">Stack</span>
                </div>
            </div>

            {{-- Hero text --}}
            <div class="relative z-[1]">
                <div
                    class="inline-block font-space text-[10px] text-accent-cyan border border-accent-cyan/30 px-2.5 py-1 rounded mb-5 tracking-[2px] uppercase">
                    IaaS Simulation Platform
                </div>
                <h1 class="text-[34px] font-extrabold leading-[1.15] text-ink-primary mb-4">
                    Cloud Infrastructure,<br>
                    <span
                        style="background: linear-gradient(90deg, #4d9bff, #00d4ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        Simplified.
                    </span>
                </h1>
                <p class="text-[14px] text-ink-muted leading-[1.7] max-w-[320px]">
                    Kelola sumber daya komputasi virtual, provisioning storage, dan akses kredensial API dalam satu
                    platform terpadu berbasis MiniStack.
                </p>
            </div>

            {{-- Stats --}}
            <div class="flex gap-4 relative z-[1]">
                @foreach ([['99.9%', 'Uptime'], ['S3', 'Compatible'], ['AES', 'Encrypted']] as $stat)
                    <div class="bg-white/[0.03] border border-rim rounded-[10px] px-4 py-3 flex-1">
                        <div class="font-space text-[18px] font-bold text-accent-bright">{{ $stat[0] }}</div>
                        <div class="text-[10px] text-ink-dim mt-0.5 tracking-[0.5px] uppercase">{{ $stat[1] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── Right panel (form) ───────────────────────────── --}}
        <div class="w-full md:w-[400px] bg-card flex flex-col justify-center px-10 py-12 border-l border-rim">
            <h2 class="text-[24px] font-bold text-ink-primary mb-1.5">Selamat Datang</h2>
            <p class="text-[13px] text-ink-muted mb-8">Masuk ke akun MiniStack Anda</p>

            @if (session('error'))
                <div class="alert alert-error mb-5">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-[18px]">
                    <label class="field-label" for="email">Email Address</label>
                    <div class="relative">
                        <span
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-dim text-[15px] pointer-events-none">✉</span>
                        <input type="email" id="email" name="email"
                            class="field-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            placeholder="user@ministack.io" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                    @error('email')
                        <div class="invalid-msg">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-6">
                    <label class="field-label" for="password">Password</label>
                    <div class="relative">
                        <span
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-dim text-[15px] pointer-events-none">🔒</span>
                        <input type="password" id="password" name="password"
                            class="field-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            placeholder="••••••••" required autocomplete="current-password">
                    </div>
                    @error('password')
                        <div class="invalid-msg">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember + Forgot --}}
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center gap-2 text-[13px] text-ink-muted cursor-pointer">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                            class="w-4 h-4 accent-accent cursor-pointer">
                        Ingat saya
                    </label>
                    <a href="{{ route('password.request') }}"
                        class="text-[13px] text-accent-bright no-underline transition-colors hover:text-accent-cyan">
                        Lupa password?
                    </a>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full relative overflow-hidden bg-grad-accent border-0 rounded-lg px-4 py-3.5
                               font-syne text-[15px] font-bold text-white cursor-pointer tracking-[0.3px]
                               shadow-accent transition-all hover:-translate-y-px hover:shadow-accent-lg active:translate-y-0">
                    Masuk ke Dashboard →
                </button>
            </form>

            <div class="relative text-center my-6">
                <div class="absolute top-1/2 left-0 right-0 h-px bg-rim"></div>
                <span class="relative bg-card px-3 text-[12px] text-ink-dim">atau</span>
            </div>

            <div class="text-center text-[13px] text-ink-muted">
                Belum punya akun?
                <a href="{{ route('register') }}"
                    class="text-accent-bright no-underline font-semibold hover:text-accent-cyan">
                    Daftar sekarang
                </a>
            </div>
        </div>
    </div>

</body>

</html>
