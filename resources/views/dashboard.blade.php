@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

    {{-- ── Welcome banner ────────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-2xl border border-rim-light px-7 py-6 mb-6 flex items-center justify-between gap-4"
        style="background: linear-gradient(135deg, #0d1a31 0%, #091220 100%)">
        {{-- Glow orb --}}
        <div class="absolute top-[-60px] right-[60px] w-[200px] h-[200px] rounded-full pointer-events-none"
            style="background: radial-gradient(circle, rgba(26,108,246,0.12) 0%, transparent 70%)"></div>

        <div>
            <h2 class="text-[20px] font-extrabold text-ink-primary mb-1">
                Selamat datang,
                <span
                    style="background: linear-gradient(90deg, #4d9bff, #00d4ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    {{ auth()->user()->name ?? 'User' }}
                </span> 👋
            </h2>
            <p class="text-[13px] text-ink-muted">Infrastruktur virtual Anda berjalan normal. Semua sistem operasional.</p>
        </div>

        <div class="font-space text-[11px] text-ink-dim text-right flex-shrink-0 max-sm:text-left" id="liveTime">
            <strong id="clock" class="block text-[22px] text-ink-secondary mb-0.5">--:--:--</strong>
            <span>ID-JKT1 Region</span>
        </div>
    </div>

    {{-- ── Stat cards ──────────────────────────────────────────── --}}
    <div class="grid-4">

        {{-- Storage --}}
        <div
            class="relative overflow-hidden bg-card border border-rim rounded-2xl px-[22px] py-5 transition-all duration-200 hover:border-rim-light hover:-translate-y-0.5">
            <div class="absolute top-0 right-0 w-20 h-20 rounded-full opacity-40 blur-3xl bg-accent pointer-events-none">
            </div>
            <div class="flex items-center justify-between mb-4">
                <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase">Storage Terpakai</div>
                <div class="w-9 h-9 rounded-[9px] flex items-center justify-center text-[16px] bg-accent/[0.15]">🗄</div>
            </div>
            <div class="text-[30px] font-extrabold text-ink-primary leading-none mb-1.5">
                {{ $storageUsed ?? '4.5' }} <small class="text-[14px] text-ink-muted">GB</small>
            </div>
            <div class="text-[12px] text-ink-muted">dari <strong>{{ $storageQuota ?? 10 }} GB</strong> kuota</div>
        </div>

        {{-- Buckets --}}
        <div
            class="relative overflow-hidden bg-card border border-rim rounded-2xl px-[22px] py-5 transition-all duration-200 hover:border-rim-light hover:-translate-y-0.5">
            <div
                class="absolute top-0 right-0 w-20 h-20 rounded-full opacity-40 blur-3xl bg-accent-cyan pointer-events-none">
            </div>
            <div class="flex items-center justify-between mb-4">
                <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase">Bucket Aktif</div>
                <div class="w-9 h-9 rounded-[9px] flex items-center justify-center text-[16px] bg-accent-cyan/[0.12]">📁
                </div>
            </div>
            <div class="text-[30px] font-extrabold text-ink-primary leading-none mb-1.5">{{ $totalBuckets ?? 0 }}</div>
            <div class="text-[12px] text-ink-muted">
                @if (($totalBuckets ?? 0) > 0)
                    <span class="text-accent-green font-bold">↑ Semua online</span>
                @else
                    Belum ada bucket
                @endif
            </div>
        </div>

        {{-- Access Keys --}}
        <div
            class="relative overflow-hidden bg-card border border-rim rounded-2xl px-[22px] py-5 transition-all duration-200 hover:border-rim-light hover:-translate-y-0.5">
            <div
                class="absolute top-0 right-0 w-20 h-20 rounded-full opacity-40 blur-3xl bg-accent-green pointer-events-none">
            </div>
            <div class="flex items-center justify-between mb-4">
                <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase">Access Keys</div>
                <div class="w-9 h-9 rounded-[9px] flex items-center justify-center text-[16px] bg-accent-green/[0.12]">🔑
                </div>
            </div>
            <div class="text-[30px] font-extrabold text-ink-primary leading-none mb-1.5">{{ $totalKeys ?? 0 }}</div>
            <div class="text-[12px] text-ink-muted">
                @if (($totalKeys ?? 0) > 0)
                    <span class="text-accent-green font-bold">↑ Aktif</span>
                @else
                    Belum ada key
                @endif
            </div>
        </div>

        {{-- Plan --}}
        <div
            class="relative overflow-hidden bg-card border border-rim rounded-2xl px-[22px] py-5 transition-all duration-200 hover:border-rim-light hover:-translate-y-0.5">
            <div
                class="absolute top-0 right-0 w-20 h-20 rounded-full opacity-40 blur-3xl bg-accent-orange pointer-events-none">
            </div>
            <div class="flex items-center justify-between mb-4">
                <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase">Paket Aktif</div>
                <div class="w-9 h-9 rounded-[9px] flex items-center justify-center text-[16px] bg-accent-orange/[0.12]">📦
                </div>
            </div>
            <div class="text-[18px] font-extrabold text-ink-primary pt-1.5 mb-1.5">
                {{ $storageSub->displayName() }}
            </div>
            <div class="text-[12px] text-ink-muted mb-3">
                <span class="text-accent-green font-bold">Aktif hingga
                    {{ $storageSub?->expires_at?->format('d M Y') ?? 'Lifetime' }}</span>
            </div>
            @if(($storageSub->plan ?? 'free') === 'free')
                <a href="{{ route('subscriptions.index') }}" class="text-[11px] text-accent-bright font-bold hover:text-accent-cyan no-underline transition-colors">⚡ Upgrade Sekarang →</a>
            @else
                <a href="{{ route('billing.index') }}" class="text-[11px] text-ink-dim hover:text-ink-muted no-underline transition-colors">Lihat Billing →</a>
            @endif
        </div>
    </div>

    {{-- ── Upgrade banner (Free users only) ────────────────────────── --}}
    @if(($storageSub->plan ?? 'free') === 'free')
    <div class="relative overflow-hidden rounded-2xl border border-accent/25 px-6 py-4 mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
        style="background: linear-gradient(135deg, rgba(26,108,246,0.10) 0%, rgba(0,212,255,0.04) 100%)">
        <div class="absolute right-[-20px] top-[-30px] w-[150px] h-[150px] rounded-full pointer-events-none opacity-40"
            style="background: radial-gradient(circle, rgba(26,108,246,0.2) 0%, transparent 70%)"></div>
        <div class="flex items-center gap-4">
            <div class="text-[28px]">🚀</div>
            <div>
                <div class="text-[14px] font-bold text-ink-primary">Tingkatkan Kapasitas Cloud Anda</div>
                @php
                    $plans = \App\Models\StorageSubscription::availablePlans();
                @endphp
                <div class="text-[12px] text-ink-muted mt-0.5">Paket Free terbatas {{ $plans['free']['quota_gb'] }} GB & {{ $plans['free']['bucket_limit'] }} Bucket. Upgrade ke <strong class="text-ink-secondary">Pro (Rp {{ number_format($plans['starter']['price'],0,',','.') }}/bln)</strong> untuk {{ $plans['starter']['quota_gb'] }} GB, atau <strong class="text-ink-secondary">Business (Rp {{ number_format($plans['pro']['price'],0,',','.') }}/bln)</strong> untuk {{ $plans['pro']['quota_gb'] }} GB + fitur enterprise.</div>
            </div>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('subscriptions.checkout', 'starter') }}" class="btn btn-primary text-[12px] py-2 px-4">Pro →</a>
            <a href="{{ route('subscriptions.checkout', 'pro') }}" class="btn text-[12px] py-2 px-4" style="background:rgba(139,92,246,0.15);border-color:rgba(139,92,246,0.4);color:#a78bfa;">Business →</a>
        </div>
    </div>
    @endif

    {{-- ── Quota + Credentials ──────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

        {{-- Quota card --}}
        <div class="bg-card border border-rim rounded-2xl px-[22px] py-5">
            <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase mb-[18px]">Sisa Kuota</div>

            @php
                $fallbackBucket = \App\Models\StorageSubscription::availablePlans()['free']['bucket_limit'];
                $bucketLimitDash = ($bucketLimit ?? $storageSub->bucket_limit ?? $fallbackBucket) >= 9999 ? '∞' : ($bucketLimit ?? $storageSub->bucket_limit ?? $fallbackBucket);
            @endphp
            @foreach ([
                ['icon' => '🗄', 'label' => 'Storage',       'used' => $storageUsed ?? 0,  'total' => $storageQuota ?? $storageSub->quota_gb ?? \App\Models\StorageSubscription::availablePlans()['free']['quota_gb'], 'unit' => 'GB',     'pct' => $storagePercent ?? 0, 'grad' => 'from-accent to-accent-cyan'],
                ['icon' => '📂', 'label' => 'Bucket Limit',  'used' => $totalBuckets ?? 0, 'total' => $bucketLimitDash,                                         'unit' => 'bucket', 'pct' => $bucketPercent ?? 0,  'grad' => 'from-accent-cyan to-accent-green'],
                ['icon' => '🔑', 'label' => 'Access Keys',   'used' => $totalKeys ?? 0,    'total' => $keyLimit ?? $storageSub->access_key_limit ?? \App\Models\StorageSubscription::availablePlans()['free']['access_key_limit'],          'unit' => 'keys',   'pct' => $keyPercent ?? 0,     'grad' => 'from-accent-orange to-accent-red'],
                ['icon' => '⚙', 'label' => 'Compute Units',  'used' => $computeUsed ?? 0,  'total' => $computeLimit ?? \App\Models\ComputeSubscription::availablePlans()['free']['compute_units'],                                     'unit' => 'vCPU·h', 'pct' => $computePercent ?? 0,  'grad' => 'from-accent-purple to-accent'],
            ] as $q)
                <div class="mb-4 last:mb-0">
                    <div class="flex items-center justify-between mb-[7px]">
                        <div class="text-[13px] text-ink-secondary flex items-center gap-[7px]">
                            {{ $q['icon'] }} {{ $q['label'] }}
                        </div>
                        <div class="font-space text-[11px] text-ink-muted">
                            <strong class="text-accent-bright">{{ $q['used'] }}</strong> / {{ $q['total'] }}
                            {{ $q['unit'] }}
                        </div>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill bg-gradient-to-r {{ $q['grad'] }}"
                            style="width: {{ $q['pct'] }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Credentials card --}}
        <div class="bg-card border border-rim rounded-2xl px-[22px] py-5">
            <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase mb-4">Kredensial API Anda</div>

            @if ($latestCredential ?? false)
                @foreach ([['label' => 'Access Key', 'id' => 'accessKey', 'value' => $latestCredential->access_key, 'btn_action' => "copyText('accessKey')", 'btn_icon' => '📋'], ['label' => 'Secret Key', 'id' => 'secretKey', 'value' => $latestCredential->secret_key_masked ?? '••••••••••••••••••••', 'btn_action' => 'revealSecret()', 'btn_icon' => '👁']] as $cred)
                    <div class="flex items-center gap-2.5 mb-2.5">
                        <div class="text-[11px] text-ink-muted w-[90px] flex-shrink-0">{{ $cred['label'] }}</div>
                        <div id="{{ $cred['id'] }}"
                            class="font-space text-[12px] text-accent-bright bg-accent/[0.06] border border-accent/[0.15] rounded-lg px-3 py-1.5 flex-1 overflow-hidden text-ellipsis whitespace-nowrap tracking-[0.5px]">
                            {{ $cred['value'] }}
                        </div>
                        <button onclick="{{ $cred['btn_action'] }}"
                            class="bg-accent/10 border border-accent/20 rounded-lg text-accent-bright text-[13px] px-2.5 py-1.5 cursor-pointer flex-shrink-0 transition-colors hover:bg-accent/20">
                            {{ $cred['btn_icon'] }}
                        </button>
                    </div>
                @endforeach

                <div class="flex items-center gap-2.5 mb-2.5">
                    <div class="text-[11px] text-ink-muted w-[90px] flex-shrink-0">Endpoint</div>
                    <div id="endpoint"
                        class="font-space text-[12px] text-accent-bright bg-accent/[0.06] border border-accent/[0.15] rounded-lg px-3 py-1.5 flex-1 overflow-hidden text-ellipsis whitespace-nowrap tracking-[0.5px]">
                        {{ config('ministack.endpoint', 'http://localhost:9000') }}
                    </div>
                    <button onclick="copyText('endpoint')"
                        class="bg-accent/10 border border-accent/20 rounded-lg text-accent-bright text-[13px] px-2.5 py-1.5 cursor-pointer flex-shrink-0 transition-colors hover:bg-accent/20">
                        📋
                    </button>
                </div>

                <div class="mt-3.5">
                    <a href="{{ route('credentials.index') }}" class="btn btn-outline text-[12px] py-[7px] px-3.5">
                        Kelola Access Keys →
                    </a>
                </div>
            @else
                <div class="text-center py-[30px]">
                    <div class="text-[32px] mb-2.5">🔑</div>
                    <div class="text-[13px] text-ink-dim mb-3.5">Belum ada kredensial. Buat access key untuk mulai
                        menggunakan API.</div>
                    <form action="{{ route('credentials.store') }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" class="btn btn-primary text-[13px] cursor-pointer">+ Buat Access Key</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Bucket storage ───────────────────────────────────────── --}}
    <div class="bg-card border border-rim rounded-2xl overflow-hidden mb-6">
        <div class="flex items-center justify-between px-[22px] py-4 border-b border-rim">
            <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase">📁 Bucket Storage Anda</div>
            <a href="{{ route('storage.index') }}"
                class="text-[12px] text-accent-bright no-underline hover:text-accent-cyan">Lihat semua →</a>
        </div>

        <div class="px-[22px] py-4">
            @if (isset($buckets) && $buckets->count() > 0)
                <div class="grid grid-cols-[repeat(auto-fill,minmax(200px,1fr))] gap-3 mt-4">
                    @foreach ($buckets as $bucket)
                        <a href="{{ route('storage.show', $bucket->id) }}"
                            class="bg-field border border-rim rounded-[10px] px-4 py-3.5 flex items-center gap-3 no-underline transition-all hover:border-accent hover:bg-card-hover">
                            <div
                                class="w-9 h-9 rounded-lg bg-accent/[0.12] flex items-center justify-center text-[16px] flex-shrink-0">
                                🪣</div>
                            <div class="overflow-hidden">
                                <div
                                    class="text-[13px] font-bold text-ink-primary whitespace-nowrap overflow-hidden text-ellipsis">
                                    {{ $bucket->name }}</div>
                                <div class="font-space text-[10px] text-ink-muted mt-0.5">
                                    {{ $bucket->size_human ?? '0 B' }}</div>
                            </div>
                        </a>
                    @endforeach

                    @if ($buckets->count() < ($bucketLimit ?? 5))
                        <a href="{{ route('storage.create') }}"
                            class="bg-field border border-dashed border-rim rounded-[10px] px-4 py-3.5 flex flex-col items-center justify-center gap-1.5 no-underline transition-all hover:border-accent hover:bg-card-hover text-center">
                            <div class="text-[22px] text-ink-dim">+</div>
                            <div class="text-[12px] text-ink-dim">Buat Bucket</div>
                        </a>
                    @endif
                </div>
            @else
                <div class="py-10 text-center text-[13px] text-ink-dim">
                    <div class="text-[36px] mb-3">🪣</div>
                    <div class="mb-3.5">Anda belum memiliki bucket storage.</div>
                    <a href="{{ route('storage.create') }}" class="btn btn-primary">+ Buat Bucket Pertama</a>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Activity log ─────────────────────────────────────────── --}}
    <div class="bg-card border border-rim rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-[22px] py-4 border-b border-rim">
            <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase">📋 Log Aktivitas Terbaru</div>
            <a href="{{ route('activity.index') }}"
                class="text-[12px] text-accent-bright no-underline hover:text-accent-cyan">Lihat semua →</a>
        </div>

        @if (isset($recentLogs) && $recentLogs->count() > 0)
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Aksi</th>
                            <th>Resource</th>
                            <th>Status</th>
                            <th>IP Address</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentLogs as $log)
                            <tr>
                                <td class="text-ink-primary font-semibold">{{ $log->action }}</td>
                                <td><span class="font-space text-[11px]">{{ $log->resource_type }}:
                                        {{ $log->resource_name }}</span></td>
                                <td>
                                    @if ($log->status === 'success')
                                        <span class="badge badge-green">✓ Sukses</span>
                                    @elseif($log->status === 'failed')
                                        <span class="badge badge-red">✕ Gagal</span>
                                    @else
                                        <span class="badge badge-orange">⏳ Proses</span>
                                    @endif
                                </td>
                                <td class="font-space text-[11px]">{{ $log->ip_address }}</td>
                                <td class="text-[12px] text-ink-muted">{{ $log->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-10 text-center text-[13px] text-ink-dim">
                <div class="text-[32px] mb-2.5">📋</div>
                <div>Belum ada aktivitas tercatat. Mulai gunakan layanan untuk melihat log di sini.</div>
            </div>
        @endif
    </div>

@endsection

@push('scripts')
    <script>
        // Live clock
        function updateClock() {
            document.getElementById('clock').textContent =
                new Date().toLocaleTimeString('id-ID', {
                    hour12: false
                });
        }
        updateClock();
        setInterval(updateClock, 1000);

        // Copy to clipboard
        function copyText(elementId) {
            const el = document.getElementById(elementId);
            if (!el) return;
            navigator.clipboard.writeText(el.textContent.trim()).then(() => {
                const btn = el.nextElementSibling;
                const original = btn.textContent;
                btn.textContent = '✓';
                btn.style.color = 'var(--tw-text-accent-green, #00e5a0)';
                setTimeout(() => {
                    btn.textContent = original;
                    btn.style.color = '';
                }, 1500);
            });
        }

        // Reveal secret key
        let secretRevealed = false;

        function revealSecret() {
            const el = document.getElementById('secretKey');
            if (!el) return;
            if (!secretRevealed) {
                fetch('{{ route('credentials.reveal', $latestCredential->id ?? 0) }}', {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.secret_key) {
                            el.textContent = data.secret_key;
                            el.style.color = '#00e5a0';
                            secretRevealed = true;
                        }
                    }).catch(() => {});
            } else {
                el.textContent = '••••••••••••••••••••';
                el.style.color = '';
                secretRevealed = false;
            }
        }
    </script>
@endpush
