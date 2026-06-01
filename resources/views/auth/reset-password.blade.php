<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiniStack — Reset Password</title>
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

    {{-- Card --}}
    <div class="relative z-10 flex w-[min(500px,96vw)] rounded-[20px] overflow-hidden border border-rim animate-card-in"
        style="box-shadow: 0 0 0 1px rgba(26,108,246,0.10), 0 40px 80px rgba(0,0,0,0.60), inset 0 1px 0 rgba(255,255,255,0.04)">

        {{-- Right panel (form) --}}
        <div class="w-full bg-card flex flex-col justify-center px-10 py-12">
            <h2 class="text-[24px] font-bold text-ink-primary mb-1.5">Reset Password</h2>
            <p class="text-[13px] text-ink-muted mb-8 leading-relaxed">
                Silakan masukkan password baru untuk akun Anda.
            </p>

            @if (session('error'))
                <div class="alert alert-error mb-5">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                
                {{-- Token --}}
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                {{-- Email --}}
                <div class="mb-5">
                    <label class="field-label" for="email">Email Address</label>
                    <div class="relative">
                        <span
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-dim text-[15px] pointer-events-none">✉</span>
                        <input type="email" id="email" name="email"
                            class="field-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            value="{{ old('email', $request->email) }}" required readonly>
                    </div>
                    @error('email')
                        <div class="invalid-msg">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-5">
                    <label class="field-label" for="password">Password Baru</label>
                    <div class="relative">
                        <span
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-dim text-[15px] pointer-events-none">🔒</span>
                        <input type="password" id="password" name="password"
                            class="field-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            placeholder="••••••••" required autofocus>
                    </div>
                    @error('password')
                        <div class="invalid-msg">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-8">
                    <label class="field-label" for="password_confirmation">Konfirmasi Password</label>
                    <div class="relative">
                        <span
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-dim text-[15px] pointer-events-none">🔒</span>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="field-input" placeholder="••••••••" required>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full relative overflow-hidden bg-grad-accent border-0 rounded-lg px-4 py-3.5
                               font-syne text-[15px] font-bold text-white cursor-pointer tracking-[0.3px]
                               shadow-accent transition-all hover:-translate-y-px hover:shadow-accent-lg active:translate-y-0">
                    Simpan Password Baru →
                </button>
            </form>
        </div>
    </div>

</body>
</html>
