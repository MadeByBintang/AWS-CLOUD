@extends('layouts.app')
@section('title', 'Paket Langganan')

@section('content')

    {{-- ── Header banner ─────────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-2xl border border-rim-light px-7 py-8 mb-8 text-center"
        style="background: linear-gradient(135deg, #0d1a31 0%, #091220 100%)">
        {{-- Orbs --}}
        <div class="absolute top-[-80px] left-[10%] w-[280px] h-[280px] rounded-full pointer-events-none"
            style="background: radial-gradient(circle, rgba(26,108,246,0.10) 0%, transparent 70%)"></div>
        <div class="absolute bottom-[-60px] right-[8%] w-[220px] h-[220px] rounded-full pointer-events-none"
            style="background: radial-gradient(circle, rgba(0,212,255,0.08) 0%, transparent 70%)"></div>

        {{-- Grid lines decoration --}}
        <div class="sub-grid-lines pointer-events-none"></div>

        <div class="relative z-10">
            <div
                class="inline-flex items-center gap-2 bg-accent/[0.10] border border-accent/[0.20] rounded-full px-4 py-1.5 mb-4">
                <span class="w-1.5 h-1.5 rounded-full bg-accent-cyan animate-pulse"></span>
                <span class="font-space text-[10px] text-accent-cyan tracking-[2px] uppercase">Pilih Paket Anda</span>
            </div>
            <h1 class="text-[28px] md:text-[36px] font-extrabold text-ink-primary leading-tight mb-3">
                Infrastruktur Cloud<br>
                <span
                    style="background: linear-gradient(90deg, #4d9bff, #00d4ff, #a78bfa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    Sesuai Kebutuhan Anda
                </span>
            </h1>
            <p class="text-[14px] text-ink-muted max-w-[520px] mx-auto">
                Dari proyek personal hingga enterprise. Skalakan infrastruktur Anda kapan saja,
                tanpa lock-in kontrak jangka panjang.
            </p>

            {{-- Toggle billing --}}
            <div class="flex items-center justify-center gap-3 mt-5">
                <span class="text-[13px] text-ink-muted" id="labelMonthly">Bulanan</span>
                <button id="billingToggle" onclick="toggleBilling()" class="sub-billing-toggle"
                    aria-label="Toggle billing period">
                    <span class="sub-billing-thumb" id="billingThumb"></span>
                </button>
                <span class="text-[13px] text-ink-muted" id="labelAnnual">
                    Tahunan
                    <span
                        class="ml-1.5 inline-flex items-center bg-accent-green/[0.15] border border-accent-green/[0.25] text-accent-green font-space text-[9px] px-2 py-[2px] rounded-full tracking-[1px]">
                        HEMAT 20%
                    </span>
                </span>
            </div>
        </div>
    </div>

    {{-- ── Plan cards ─────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-8">
        
        {{-- Free --}}
        <div class="sub-plan-card group" data-plan="free">
            <div class="sub-plan-glow bg-accent/[0.08]"></div>
            <div class="sub-plan-inner">
                <div class="sub-plan-badge bg-accent/[0.10] border-accent/[0.20] text-accent-cyan">
                    <span>🌱</span> Starter
                </div>
                <div class="sub-plan-name">Free</div>
                <div class="sub-plan-price">
                    <span class="sub-price-currency">Rp</span>
                    <span class="sub-price-amount" data-monthly="0" data-annual="0">0</span>
                    <span class="sub-price-period">/bulan</span>
                </div>
                <p class="sub-plan-desc">Sempurna untuk eksplorasi dan proyek kecil. Mulai tanpa kartu kredit.</p>

                <div class="sub-divider"></div>

                <ul class="sub-feature-list">
                    <li class="sub-feature-item sub-feature-on">
                        <span class="sub-feature-icon">✓</span>
                        <span>{{ \App\Models\StorageSubscription::availablePlans()['free']['quota_gb'] }} GB Storage</span>
                    </li>
                    <li class="sub-feature-item sub-feature-on">
                        <span class="sub-feature-icon">✓</span>
                        <span>{{ \App\Models\StorageSubscription::availablePlans()['free']['bucket_limit'] }} Bucket</span>
                    </li>
                    <li class="sub-feature-item sub-feature-on">
                        <span class="sub-feature-icon">✓</span>
                        <span>{{ \App\Models\StorageSubscription::availablePlans()['free']['access_key_limit'] }} Access Keys</span>
                    </li>
                    <li class="sub-feature-item sub-feature-on">
                        <span class="sub-feature-icon">✓</span>
                        <span>{{ \App\Models\ComputeSubscription::availablePlans()['free']['compute_units'] }} vCPU·h / bulan</span>
                    </li>
                    <li class="sub-feature-item sub-feature-off">
                        <span class="sub-feature-icon sub-feature-icon-off">✕</span>
                        <span>Compute Priority</span>
                    </li>
                    <li class="sub-feature-item sub-feature-off">
                        <span class="sub-feature-icon sub-feature-icon-off">✕</span>
                        <span>Custom Domain</span>
                    </li>
                    <li class="sub-feature-item sub-feature-off">
                        <span class="sub-feature-icon sub-feature-icon-off">✕</span>
                        <span>SLA 99.9%</span>
                    </li>
                </ul>

                @if (($storageSub->plan ?? 'free') === 'free')
                    <button class="btn sub-btn-current" disabled>
                        <span class="w-1.5 h-1.5 rounded-full bg-accent-green mr-2 animate-pulse"></span>
                        Paket Saat Ini
                    </button>
                @else
                    <a href="{{ route('subscriptions.checkout', 'free') }}" class="btn btn-outline sub-btn">
                        Pilih Free
                    </a>
                @endif
            </div>
        </div>

        {{-- Pro --}}
        <div class="sub-plan-card group sub-plan-popular" data-plan="pro">
            <div class="sub-plan-glow bg-accent/[0.15]"></div>
            <div class="sub-popular-badge">
                <span class="font-space text-[9px] tracking-[2px] uppercase inline-block translate-x-[10px]">⭐Terpopuler</span>
            </div>
            <div class="sub-plan-inner">
                <div class="sub-plan-badge bg-accent/[0.15] border-accent/[0.30] text-accent">
                    <span>🚀</span> Professional
                </div>
                <div class="sub-plan-name">Pro</div>
                <div class="sub-plan-price">
                    <span class="sub-price-currency">Rp</span>
                    <span class="sub-price-amount" data-monthly="54999" data-annual="43999">54.999</span>
                    <span class="sub-price-period">/bulan</span>
                </div>
                <p class="sub-plan-desc">Untuk developer dan tim kecil yang butuh performa lebih dan skalabilitas.</p>

                <div class="sub-divider"></div>

                <ul class="sub-feature-list">
                    <li class="sub-feature-item sub-feature-on">
                        <span class="sub-feature-icon">✓</span>
                        <span>{{ \App\Models\StorageSubscription::availablePlans()['starter']['quota_gb'] }} GB Storage</span>
                    </li>
                    <li class="sub-feature-item sub-feature-on">
                        <span class="sub-feature-icon">✓</span>
                        <span>{{ \App\Models\StorageSubscription::availablePlans()['starter']['bucket_limit'] }} Bucket</span>
                    </li>
                    <li class="sub-feature-item sub-feature-on">
                        <span class="sub-feature-icon">✓</span>
                        <span>{{ \App\Models\StorageSubscription::availablePlans()['starter']['access_key_limit'] }} Access Keys</span>
                    </li>
                    <li class="sub-feature-item sub-feature-on">
                        <span class="sub-feature-icon">✓</span>
                        <span>{{ \App\Models\ComputeSubscription::availablePlans()['starter']['compute_units'] }} vCPU·h / bulan</span>
                    </li>
                    <li class="sub-feature-item sub-feature-on">
                        <span class="sub-feature-icon">✓</span>
                        <span>Compute Priority</span>
                    </li>
                    <li class="sub-feature-item sub-feature-off">
                        <span class="sub-feature-icon sub-feature-icon-off">✕</span>
                        <span>Custom Domain</span>
                    </li>
                    <li class="sub-feature-item sub-feature-off">
                        <span class="sub-feature-icon sub-feature-icon-off">✕</span>
                        <span>SLA 99.9%</span>
                    </li>
                </ul>

                @if (($storageSub->plan ?? '') === 'starter')
                    <button class="btn sub-btn-current" disabled>
                        <span class="w-1.5 h-1.5 rounded-full bg-accent-green mr-2 animate-pulse"></span>
                        Paket Saat Ini
                    </button>
                @elseif (($storageSub->plan ?? '') === 'pro')
                    <button class="btn btn-outline sub-btn" style="opacity:0.5;cursor:not-allowed;" disabled>
                        ✓ Sudah Upgrade ke Business
                    </button>
                @else
                    <a href="{{ route('subscriptions.checkout', 'starter') }}" class="btn btn-primary sub-btn">
                        Upgrade ke Pro →
                    </a>
                @endif
            </div>
        </div>

        {{-- Business --}}
        <div class="sub-plan-card group md:col-span-2 xl:col-span-1" data-plan="business">
            <div class="sub-plan-glow bg-accent-purple/[0.08]"></div>
            <div class="sub-plan-inner">
                <div class="sub-plan-badge bg-accent-purple/[0.12] border-accent-purple/[0.25] text-accent-purple">
                    <span>🏢</span> Business
                </div>
                <div class="sub-plan-name">Business</div>
                <div class="sub-plan-price">
                    <span class="sub-price-currency">Rp</span>
                    <span class="sub-price-amount" data-monthly="119999" data-annual="95999">119.999</span>
                    <span class="sub-price-period">/bulan</span>
                </div>
                <p class="sub-plan-desc">Untuk bisnis yang memerlukan keandalan tinggi dan fitur enterprise.</p>

                <div class="sub-divider"></div>

                <ul class="sub-feature-list">
                    <li class="sub-feature-item sub-feature-on">
                        <span class="sub-feature-icon">✓</span>
                        <span>{{ \App\Models\StorageSubscription::availablePlans()['pro']['quota_gb'] }} GB Storage</span>
                    </li>
                    <li class="sub-feature-item sub-feature-on">
                        <span class="sub-feature-icon">✓</span>
                        <span>{{ \App\Models\StorageSubscription::availablePlans()['pro']['bucket_limit'] >= 9999 ? 'Unlimited' : \App\Models\StorageSubscription::availablePlans()['pro']['bucket_limit'] }} Bucket</span>
                    </li>
                    <li class="sub-feature-item sub-feature-on">
                        <span class="sub-feature-icon">✓</span>
                        <span>{{ \App\Models\StorageSubscription::availablePlans()['pro']['access_key_limit'] }} Access Keys</span>
                    </li>
                    <li class="sub-feature-item sub-feature-on">
                        <span class="sub-feature-icon">✓</span>
                        <span>{{ number_format(\App\Models\ComputeSubscription::availablePlans()['pro']['compute_units'], 0, ',', '.') }} vCPU·h / bulan</span>
                    </li>
                    <li class="sub-feature-item sub-feature-on">
                        <span class="sub-feature-icon">✓</span>
                        <span>Compute Priority</span>
                    </li>
                    <li class="sub-feature-item sub-feature-on">
                        <span class="sub-feature-icon">✓</span>
                        <span>Custom Domain</span>
                    </li>
                    <li class="sub-feature-item sub-feature-off">
                        <span class="sub-feature-icon sub-feature-icon-off">✕</span>
                        <span>SLA 99.9%</span>
                    </li>
                </ul>

                @if (($storageSub->plan ?? '') === 'pro')
                    <button class="btn sub-btn-current" disabled>
                        <span class="w-1.5 h-1.5 rounded-full bg-accent-green mr-2 animate-pulse"></span>
                        Paket Saat Ini
                    </button>
                @else
                    <a href="{{ route('subscriptions.checkout', 'pro') }}" class="btn sub-btn-purple sub-btn">
                        Upgrade ke Business →
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Current subscription info ───────────────────────── --}}
    @if ($storageSub ?? false)
        <div class="bg-card border border-rim rounded-2xl px-[22px] py-5 mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase mb-2">Langganan Aktif Anda
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-[20px] font-extrabold text-ink-primary">{{ $storageSub->displayName() }}</span>
                        <span class="badge badge-green">● Aktif</span>
                    </div>
                    <div class="text-[12px] text-ink-muted mt-1">
                        Berlaku hingga
                        <strong
                            class="text-ink-secondary">{{ $storageSub->expires_at?->format('d M Y') ?? 'Unlimited' }}</strong>
                        @if ($storageSub->expires_at && $storageSub->expires_at->diffInDays(now()) <= 7)
                            <span class="ml-2 badge badge-orange">⚠ Hampir berakhir</span>
                        @endif
                    </div>
                </div>
                <div class="flex gap-2.5 flex-shrink-0">
                    <a href="{{ route('billing.index') }}" class="btn btn-outline text-[12px] py-[7px] px-4">
                        Riwayat Pembayaran
                    </a>
                    <a href="{{ route('subscriptions.cancel') }}" class="btn sub-btn-cancel text-[12px] py-[7px] px-4"
                        onclick="return confirm('Yakin ingin membatalkan langganan?')">
                        Batalkan Langganan
                    </a>
                </div>
            </div>

            {{-- Usage bar --}}
            <div class="mt-5 grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $bucketLimitDisplay = ($storageSub->bucket_limit >= 9999) ? '∞' : $storageSub->bucket_limit;
                @endphp
                @foreach ([
                    ['icon' => '🗄', 'label' => 'Storage',     'used' => $storageUsed ?? 0,     'total' => ($storageQuota ?? $storageSub->quota_gb),               'unit' => 'GB', 'pct' => $storagePercent ?? 0, 'grad' => 'from-accent to-accent-cyan'],
                    ['icon' => '📂', 'label' => 'Bucket',      'used' => $totalBuckets ?? 0,    'total' => $bucketLimitDisplay,                                       'unit' => '',   'pct' => $bucketPercent ?? 0,  'grad' => 'from-accent-cyan to-accent-green'],
                    ['icon' => '🔑', 'label' => 'Access Keys', 'used' => $totalKeys ?? 0,       'total' => ($keyLimit ?? $storageSub->access_key_limit ?? 2),         'unit' => '',   'pct' => $keyPercent ?? 0,     'grad' => 'from-accent-orange to-accent-red'],
                    ['icon' => '⚙', 'label' => 'vCPU·h',      'used' => $computeUsed ?? 0,     'total' => ($computeLimit ?? 100),                                    'unit' => '',   'pct' => $computePercent ?? 0, 'grad' => 'from-accent-purple to-accent'],
                ] as $q)
                    <div class="bg-field border border-rim rounded-xl px-3.5 py-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] text-ink-muted">{{ $q['icon'] }} {{ $q['label'] }}</span>
                            <span class="font-space text-[10px] text-accent-bright">{{ $q['pct'] }}%</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill bg-gradient-to-r {{ $q['grad'] }}"
                                style="width: {{ $q['pct'] }}%"></div>
                        </div>
                        <div class="font-space text-[10px] text-ink-dim mt-1.5">
                            {{ $q['used'] }} / {{ $q['total'] }} {{ $q['unit'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── FAQ / Compare ──────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-8">

        {{-- Compare table --}}
        <div class="bg-card border border-rim rounded-2xl overflow-hidden">
            <div class="px-[22px] py-4 border-b border-rim">
                <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase">📊 Perbandingan Fitur</div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Fitur</th>
                            <th class="text-center">Free</th>
                            <th class="text-center text-accent">Pro</th>
                            <th class="text-center">Business</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $p = \App\Models\StorageSubscription::availablePlans();
                            $bizBucket = $p['pro']['bucket_limit'] >= 9999 ? '∞' : $p['pro']['bucket_limit'];
                            $compareRows = [
                                ['label' => 'Storage',          'free' => $p['free']['quota_gb'].' GB',    'pro' => $p['starter']['quota_gb'].' GB', 'biz' => $p['pro']['quota_gb'].' GB'],
                                ['label' => 'Bucket',           'free' => (string)$p['free']['bucket_limit'], 'pro' => (string)$p['starter']['bucket_limit'], 'biz' => $bizBucket],
                                ['label' => 'Access Keys',      'free' => (string)$p['free']['access_key_limit'], 'pro' => (string)$p['starter']['access_key_limit'], 'biz' => (string)$p['pro']['access_key_limit']],
                                ['label' => 'vCPU·h / bln',    'free' => '10',   'pro' => '500',   'biz' => '5.000'],
                                ['label' => 'Compute Priority', 'free' => false,  'pro' => true,    'biz' => true],
                                ['label' => 'Custom Domain',    'free' => false,  'pro' => false,   'biz' => true],
                                ['label' => 'SLA 99.9%',        'free' => false,  'pro' => false,   'biz' => false],
                            ];
                        @endphp
                        @foreach ($compareRows as $row)
                            <tr>
                                <td class="text-[13px] text-ink-secondary">{{ $row['label'] }}</td>
                                <td class="text-center font-space text-[12px]">
                                    @if ($row['free'] === false)
                                        <span class="text-ink-dim">—</span>
                                    @elseif ($row['free'] === true)
                                        <span class="text-accent-green">✓</span>
                                    @else
                                        <span class="text-ink-muted">{{ $row['free'] }}</span>
                                    @endif
                                </td>
                                <td class="text-center font-space text-[12px]">
                                    @if ($row['pro'] === false)
                                        <span class="text-ink-dim">—</span>
                                    @elseif ($row['pro'] === true)
                                        <span class="text-accent-green">✓</span>
                                    @else
                                        <span class="text-accent">{{ $row['pro'] }}</span>
                                    @endif
                                </td>
                                <td class="text-center font-space text-[12px]">
                                    @if ($row['biz'] === false)
                                        <span class="text-ink-dim">—</span>
                                    @elseif ($row['biz'] === true)
                                        <span class="text-accent-green">✓</span>
                                    @else
                                        <span class="text-ink-muted">{{ $row['biz'] }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- FAQ --}}
        <div class="bg-card border border-rim rounded-2xl overflow-hidden">
            <div class="px-[22px] py-4 border-b border-rim">
                <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase">❓ Pertanyaan Umum</div>
            </div>
            <div class="px-[22px] py-4 space-y-0">
                @foreach ([['q' => 'Apakah bisa upgrade/downgrade kapan saja?', 'a' => 'Ya, Anda bisa mengubah paket kapan saja. Perubahan berlaku di siklus billing berikutnya. Jika upgrade, selisih biaya akan diperhitungkan secara prorata.'], ['q' => 'Metode pembayaran apa yang diterima?', 'a' => 'Kami menerima transfer bank, virtual account, kartu kredit/debit Visa & Mastercard, serta dompet digital seperti GoPay dan OVO.'], ['q' => 'Bagaimana jika melebihi batas kuota?', 'a' => 'Layanan akan dibatasi hingga kuota tersedia kembali di bulan berikutnya, atau Anda bisa upgrade paket untuk mendapatkan kuota lebih besar.'], ['q' => 'Apakah ada masa percobaan untuk paket berbayar?', 'a' => 'Saat ini belum tersedia free trial untuk paket berbayar. Namun, paket Free tersedia tanpa batas waktu untuk eksplorasi.'], ['q' => 'Apakah data saya aman saat downgrade?', 'a' => 'Data Anda tetap aman. Namun jika melebihi batas kuota paket baru, operasi write akan diblokir hingga penggunaan kembali normal.']] as $i => $faq)
                    <div class="sub-faq-item {{ $i > 0 ? 'border-t border-rim' : '' }}">
                        <button class="sub-faq-trigger" onclick="toggleFaq(this)">
                            <span
                                class="text-[13px] font-semibold text-ink-secondary text-left">{{ $faq['q'] }}</span>
                            <span class="sub-faq-icon flex-shrink-0 text-ink-dim">+</span>
                        </button>
                        <div class="sub-faq-body hidden">
                            <p class="text-[12px] text-ink-muted leading-relaxed pb-3.5">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Trust strip ─────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @foreach ([['icon' => '🔒', 'title' => 'Keamanan Data', 'desc' => 'Enkripsi AES-256 end-to-end untuk semua data Anda'], ['icon' => '⚡', 'title' => 'Uptime 99.95%', 'desc' => 'Infrastruktur berlapis dengan failover otomatis'], ['icon' => '🇮🇩', 'title' => 'Data Center Lokal', 'desc' => 'Server berlokasi di Jakarta, latensi rendah'], ['icon' => '💬', 'title' => 'Support Responsif', 'desc' => 'Tim siap membantu via email & live chat']] as $trust)
            <div class="bg-card border border-rim rounded-2xl px-4 py-4 text-center">
                <div class="text-[24px] mb-2">{{ $trust['icon'] }}</div>
                <div class="text-[13px] font-bold text-ink-secondary mb-1">{{ $trust['title'] }}</div>
                <div class="text-[11px] text-ink-dim leading-relaxed">{{ $trust['desc'] }}</div>
            </div>
        @endforeach
    </div>

@endsection

@push('scripts')
    <script>
        let isAnnual = false;

        function toggleBilling() {
            isAnnual = !isAnnual;
            const thumb = document.getElementById('billingThumb');
            const toggle = document.getElementById('billingToggle');
            const lM = document.getElementById('labelMonthly');
            const lA = document.getElementById('labelAnnual');

            thumb.classList.toggle('translate-x-[18px]', isAnnual);
            toggle.classList.toggle('sub-billing-toggle-on', isAnnual);
            lM.classList.toggle('text-ink-primary', !isAnnual);
            lA.classList.toggle('text-ink-primary', isAnnual);

            document.querySelectorAll('.sub-price-amount').forEach(el => {
                const monthly = el.dataset.monthly;
                const annual = el.dataset.annual;
                if (!monthly || monthly === 'custom') return;

                const val = isAnnual ? parseInt(annual) : parseInt(monthly);
                el.textContent = val.toLocaleString('id-ID');
            });
        }

        function toggleFaq(btn) {
            const body = btn.nextElementSibling;
            const icon = btn.querySelector('.sub-faq-icon');
            const isOpen = !body.classList.contains('hidden');

            // close all
            document.querySelectorAll('.sub-faq-body').forEach(b => b.classList.add('hidden'));
            document.querySelectorAll('.sub-faq-icon').forEach(i => {
                i.textContent = '+';
                i.classList.remove('rotate-45');
            });

            if (!isOpen) {
                body.classList.remove('hidden');
                icon.textContent = '×';
            }
        }
    </script>
@endpush
