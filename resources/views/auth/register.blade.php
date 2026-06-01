<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiniStack — Daftar Akun</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Syne:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body
    class="font-syne bg-deep text-ink-primary min-h-screen flex items-center justify-center overflow-x-hidden relative py-6">

    {{-- Animated grid background --}}
    <div class="bg-grid-anim"></div>

    {{-- Orbs --}}
    <div class="fixed w-[500px] h-[500px] rounded-full pointer-events-none blur-[80px] top-[-150px] right-[-150px] animate-orb-float-slow"
        style="background: rgba(26,108,246,0.10)"></div>
    <div class="fixed w-[350px] h-[350px] rounded-full pointer-events-none blur-[80px] bottom-[-80px] left-[-80px] animate-orb-float-slow-rev"
        style="background: rgba(0,229,160,0.06)"></div>

    {{-- Scanlines --}}
    <div class="scanlines-overlay"></div>

    {{-- Register card --}}
    <div class="relative z-10 flex w-[min(960px,96vw)] rounded-[20px] overflow-hidden border border-rim animate-card-in"
        style="box-shadow: 0 0 0 1px rgba(26,108,246,0.08), 0 40px 80px rgba(0,0,0,0.55), inset 0 1px 0 rgba(255,255,255,0.035)">

        {{-- ── Left panel ──────────────────────────────────── --}}
        <div class="flex-1 hidden md:flex flex-col justify-between px-9 py-12 relative overflow-hidden"
            style="background: linear-gradient(150deg, #0a1526 0%, #091220 60%, #0b1828 100%)">
            {{-- Glow orbs --}}
            <div class="absolute top-[-60px] left-[-60px] w-[280px] h-[280px] rounded-full pointer-events-none"
                style="background: radial-gradient(circle, rgba(0,229,160,0.10) 0%, transparent 70%)"></div>
            <div class="absolute bottom-[-60px] right-[-60px] w-[220px] h-[220px] rounded-full pointer-events-none"
                style="background: radial-gradient(circle, rgba(26,108,246,0.14) 0%, transparent 70%)"></div>

            {{-- Brand --}}
            <div class="flex items-center gap-3 relative z-[1]">
                <div class="w-10 h-10 bg-grad-accent-diag rounded-[10px] flex items-center justify-center text-[19px]"
                    style="box-shadow: 0 0 18px rgba(26,108,246,0.38)">☁</div>
                <div class="text-[21px] font-extrabold tracking-[-0.4px] text-ink-primary">
                    Mini<span class="text-accent-cyan">Stack</span>
                </div>
            </div>

            {{-- Registration steps --}}
            <div class="relative z-[1]">
                <div class="font-space text-[10px] text-accent-cyan tracking-[2px] uppercase mb-5">// Proses Registrasi
                </div>

                @php
                    $steps = [
                        [
                            'num' => '01',
                            'state' => 'active',
                            'title' => 'Buat Akun',
                            'desc' => 'Isi data diri dan buat password yang kuat untuk akun Anda.',
                        ],
                        [
                            'num' => '02',
                            'state' => 'pending',
                            'title' => 'Pilih Paket',
                            'desc' => 'Pilih paket Free, Starter, atau Professional sesuai kebutuhan.',
                        ],
                        [
                            'num' => '03',
                            'state' => 'pending',
                            'title' => 'Provisioning Otomatis',
                            'desc' => 'Sistem akan membuat bucket dan access key secara otomatis.',
                        ],
                        [
                            'num' => '04',
                            'state' => 'pending',
                            'title' => 'Dashboard Aktif',
                            'desc' => 'Infrastruktur virtual Anda siap digunakan.',
                        ],
                    ];
                @endphp

                @foreach ($steps as $step)
                    <div class="flex items-start gap-3.5 {{ !$loop->last ? 'mb-5' : '' }}">
                        <div
                            class="w-7 h-7 rounded-lg flex items-center justify-center font-space text-[11px] font-bold flex-shrink-0 mt-[1px]
                        {{ $step['state'] === 'active' ? 'bg-accent/20 text-accent-bright border border-accent/35' : '' }}
                        {{ $step['state'] === 'done' ? 'bg-accent-green/15 text-accent-green border border-accent-green/25' : '' }}
                        {{ $step['state'] === 'pending' ? 'bg-white/[0.03] text-ink-dim border border-rim' : '' }}">
                            {{ $step['num'] }}
                        </div>
                        <div class="flex-1">
                            <div
                                class="text-[13px] font-bold {{ $step['state'] === 'pending' ? 'text-ink-muted' : 'text-ink-primary' }} mb-0.5">
                                {{ $step['title'] }}
                            </div>
                            <div class="text-[12px] text-ink-dim leading-[1.5]">{{ $step['desc'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Security badge --}}
            <div
                class="relative z-[1] flex items-center gap-2.5 bg-accent-green/[0.06] border border-accent-green/[0.15] rounded-[10px] px-4 py-3">
                <span class="text-[18px]">🔒</span>
                <div class="text-[12px] text-ink-muted leading-[1.5]">
                    <strong class="text-accent-green">Enkripsi AES-256</strong><br>
                    Data dan kredensial Anda disimpan dengan aman menggunakan enkripsi standar industri.
                </div>
            </div>
        </div>

        {{-- ── Right panel (form) ───────────────────────────── --}}
        <div
            class="w-full md:w-[430px] bg-card flex flex-col justify-center px-10 py-11 border-l border-rim overflow-y-auto">
            <h2 class="text-[23px] font-extrabold text-ink-primary mb-1">Buat Akun Baru</h2>
            <p class="text-[13px] text-ink-muted mb-7">Bergabung dengan MiniStack — gratis selamanya untuk paket Free.
            </p>

            @if (session('error'))
                <div class="alert alert-error mb-[18px]">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" id="registerForm">
                @csrf

                {{-- Name --}}
                <div class="mb-4">
                    <label class="field-label" for="name">Nama Lengkap</label>
                    <div class="relative">
                        <span
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-dim text-[14px] pointer-events-none">👤</span>
                        <input type="text" id="name" name="name"
                            class="field-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            placeholder="Nama lengkap Anda" value="{{ old('name') }}" required autocomplete="name">
                    </div>
                    @error('name')
                        <div class="invalid-msg">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label class="field-label" for="email">Email Address</label>
                    <div class="relative">
                        <span
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-dim text-[14px] pointer-events-none">✉</span>
                        <input type="email" id="email" name="email"
                            class="field-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            placeholder="email@domain.com" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                    @error('email')
                        <div class="invalid-msg">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <label class="field-label" for="password">Password</label>
                    <div class="relative">
                        <span
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-dim text-[14px] pointer-events-none">🔒</span>
                        <input type="password" id="password" name="password"
                            class="field-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            placeholder="Minimal 8 karakter" required autocomplete="new-password"
                            oninput="checkStrength(this.value)">
                    </div>
                    {{-- Strength bars --}}
                    <div class="flex gap-1 mt-1.5" id="pwdBars">
                        <div class="flex-1 h-[3px] rounded-sm bg-rim transition-colors duration-300" id="bar1">
                        </div>
                        <div class="flex-1 h-[3px] rounded-sm bg-rim transition-colors duration-300" id="bar2">
                        </div>
                        <div class="flex-1 h-[3px] rounded-sm bg-rim transition-colors duration-300" id="bar3">
                        </div>
                        <div class="flex-1 h-[3px] rounded-sm bg-rim transition-colors duration-300" id="bar4">
                        </div>
                    </div>
                    <div class="font-space text-[10px] text-ink-dim mt-1" id="pwdLabel"></div>
                    @error('password')
                        <div class="invalid-msg">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Confirm password --}}
                <div class="mb-4">
                    <label class="field-label" for="password_confirmation">Konfirmasi Password</label>
                    <div class="relative">
                        <span
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-dim text-[14px] pointer-events-none">🔒</span>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="field-input {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                            placeholder="Ulangi password" required autocomplete="new-password">
                    </div>
                    @error('password_confirmation')
                        <div class="invalid-msg">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Terms checkbox --}}
                <div class="flex items-start gap-2.5 mb-5 mt-1">
                    <input type="checkbox" id="terms" name="terms" required
                        class="w-4 h-4 mt-0.5 accent-accent flex-shrink-0 cursor-pointer">
                    <label for="terms" class="text-[12px] text-ink-muted leading-[1.6]">
                        Saya menyetujui
                        <a href="#" onclick="event.preventDefault(); event.stopPropagation(); openModal('terms');" class="text-accent-bright no-underline hover:text-accent-cyan relative z-10 cursor-pointer">Syarat &amp;
                            Ketentuan</a>
                        serta
                        <a href="#" onclick="event.preventDefault(); event.stopPropagation(); openModal('privacy');" class="text-accent-bright no-underline hover:text-accent-cyan relative z-10 cursor-pointer">Kebijakan
                            Privasi</a>
                        MiniStack. Data saya akan diproses sesuai ketentuan yang berlaku.
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full relative overflow-hidden bg-grad-accent border-0 rounded-lg px-4 py-3.5
                               font-syne text-[15px] font-bold text-white cursor-pointer tracking-[0.3px]
                               shadow-accent transition-all hover:-translate-y-px hover:shadow-accent-lg active:translate-y-0">
                    Buat Akun Sekarang →
                </button>
            </form>

            <div class="text-center text-[13px] text-ink-muted mt-5">
                Sudah punya akun?
                <a href="{{ route('login') }}"
                    class="text-accent-bright no-underline font-semibold hover:text-accent-cyan">
                    Masuk di sini
                </a>
            </div>
        </div>
        </div>
    </div>

    {{-- ── Custom Modal ─────────────────────────────────────── --}}
    <div id="customModal" class="fixed inset-0 z-[200] flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-deep/80 backdrop-blur-sm" onclick="closeModal()"></div>
        
        {{-- Modal Box --}}
        <div class="relative bg-card w-[min(500px,90vw)] max-h-[80vh] border border-rim rounded-2xl shadow-2xl flex flex-col transform scale-95 transition-transform duration-300" id="modalBox">
            <div class="px-6 py-5 border-b border-rim/50 flex items-center justify-between">
                <h3 class="text-[18px] font-bold text-ink-primary m-0 flex items-center gap-2" id="modalTitle">
                    {{-- Title will be injected via JS --}}
                </h3>
                <button onclick="closeModal()" class="w-8 h-8 rounded-lg bg-base/50 flex items-center justify-center text-ink-dim hover:text-ink-primary hover:bg-rim transition-colors cursor-pointer border-0">
                    ✕
                </button>
            </div>
            <div class="px-6 py-6 overflow-y-auto text-[14px] text-ink-muted leading-[1.7]" id="modalContent">
                {{-- Content will be injected via JS --}}
            </div>
            <div class="px-6 py-4 border-t border-rim/50 flex justify-end bg-base/30 rounded-b-2xl">
                <button onclick="closeModal()" class="bg-accent text-white font-bold py-2 px-5 rounded-lg text-[13px] hover:bg-accent-light transition-colors border-0 cursor-pointer shadow-accent">
                    Mengerti
                </button>
            </div>
        </div>
    </div>

    <script>
        function checkStrength(val) {
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const colors = ['', '#ff4466', '#ff4466', '#4d9bff', '#00e5a0', '#00e5a0'];
            const labels = ['', 'Lemah', 'Lemah', 'Sedang', 'Kuat', 'Sangat Kuat'];

            for (let i = 1; i <= 4; i++) {
                const bar = document.getElementById('bar' + i);
                bar.style.background = i <= score ? colors[score] : '';
            }

            const label = document.getElementById('pwdLabel');
            label.textContent = val.length > 0 ? labels[score] : '';
            label.style.color = colors[score] || '';
        }

        // Modal Logic
        const modalData = {
            terms: {
                title: '📄 Syarat & Ketentuan',
                content: '<p class="mb-3">Dengan mendaftar dan menggunakan layanan MiniStack, Anda menyetujui persyaratan berikut:</p><ul class="list-disc pl-5 mb-3 space-y-1"><li>Anda tidak akan menggunakan infrastruktur cloud ini untuk kegiatan ilegal (spamming, mining, dll).</li><li>Sistem bersifat simulasi, namun penggunaan resource tetap diawasi.</li><li>MiniStack berhak menghentikan layanan kapan saja apabila ditemukan pelanggaran.</li></ul><p>Ketentuan ini dapat berubah sewaktu-waktu tanpa pemberitahuan sebelumnya.</p>'
            },
            privacy: {
                title: '🛡️ Kebijakan Privasi',
                content: '<p class="mb-3">Privasi Anda sangat penting bagi kami. Berikut adalah ringkasannya:</p><ul class="list-disc pl-5 mb-3 space-y-1"><li>Data kredensial dan API keys Anda dienkripsi menggunakan standar industri AES-256.</li><li>Kami tidak pernah menjual data pribadi Anda kepada pihak ketiga.</li><li>Data sesi Anda digunakan secara eksklusif untuk memberikan pengalaman simulasi AWS yang lebih baik.</li></ul>'
            }
        };

        const modalOverlay = document.getElementById('customModal');
        const modalBox = document.getElementById('modalBox');

        function openModal(type) {
            document.getElementById('modalTitle').innerHTML = modalData[type].title;
            document.getElementById('modalContent').innerHTML = modalData[type].content;
            
            modalOverlay.classList.remove('opacity-0', 'pointer-events-none');
            modalBox.classList.remove('scale-95');
        }

        function closeModal() {
            modalOverlay.classList.add('opacity-0', 'pointer-events-none');
            modalBox.classList.add('scale-95');
        }
    </script>

</body>

</html>
