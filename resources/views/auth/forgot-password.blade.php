<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiniStack — Lupa Password</title>
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

    {{-- Floating Toast Container for Reset Link Simulation --}}
    <div id="toastContainer" class="fixed top-6 right-6 z-[100] flex flex-col gap-3">
        @if (session('success'))
            <div class="toast-message bg-card border border-rim shadow-xl rounded-xl px-4 py-4 flex items-start gap-3 transform translate-y-[-20px] opacity-0 transition-all duration-500 pointer-events-auto" style="animation: slideDownFade 0.5s forwards;">
                <div class="mt-0.5 text-accent-green">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div>
                    <div class="text-[14px] font-bold text-ink-primary">Link Reset Berhasil Dibuat</div>
                    <div class="text-[13px] text-ink-muted mt-1 leading-relaxed">
                        {{ session('success') }}
                        @if(session('reset_link'))
                        <div class="mt-3">
                            <a href="{{ session('reset_link') }}" class="inline-block bg-accent text-white px-4 py-2 rounded-lg text-[13px] font-bold no-underline hover:bg-accent-light transition-colors">
                                Reset Password Sekarang →
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                <button onclick="this.closest('.toast-message').remove()" class="ml-2 text-ink-dim hover:text-ink-primary focus:outline-none">×</button>
            </div>
        @endif
    </div>

    <style>
        @keyframes slideDownFade {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>

    {{-- Card --}}
    <div class="relative z-10 flex w-[min(500px,96vw)] rounded-[20px] overflow-hidden border border-rim animate-card-in"
        style="box-shadow: 0 0 0 1px rgba(26,108,246,0.10), 0 40px 80px rgba(0,0,0,0.60), inset 0 1px 0 rgba(255,255,255,0.04)">

        {{-- Right panel (form) --}}
        <div class="w-full bg-card flex flex-col justify-center px-10 py-12">
            <h2 class="text-[24px] font-bold text-ink-primary mb-1.5">Lupa Password?</h2>
            <p class="text-[13px] text-ink-muted mb-8 leading-relaxed">
                Tidak masalah. Masukkan alamat email Anda dan sistem kami akan mengirimkan link untuk mereset password Anda.
            </p>

            @if (session('error'))
                <div class="alert alert-error mb-5">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-6">
                    <label class="field-label" for="email">Email Address</label>
                    <div class="relative">
                        <span
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-dim text-[15px] pointer-events-none">✉</span>
                        <input type="email" id="email" name="email"
                            class="field-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            placeholder="user@ministack.io" value="{{ old('email') }}" required autofocus>
                    </div>
                    @error('email')
                        <div class="invalid-msg">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full relative overflow-hidden bg-grad-accent border-0 rounded-lg px-4 py-3.5
                               font-syne text-[15px] font-bold text-white cursor-pointer tracking-[0.3px]
                               shadow-accent transition-all hover:-translate-y-px hover:shadow-accent-lg active:translate-y-0">
                    Kirim Link Reset →
                </button>
            </form>

            <div class="relative text-center my-6">
                <div class="absolute top-1/2 left-0 right-0 h-px bg-rim"></div>
            </div>

            <div class="text-center text-[13px] text-ink-muted">
                Ingat password Anda?
                <a href="{{ route('login') }}"
                    class="text-accent-bright no-underline font-semibold hover:text-accent-cyan">
                    Kembali ke Login
                </a>
            </div>
        </div>
    </div>

</body>
</html>
