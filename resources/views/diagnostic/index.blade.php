@extends('layouts.app')
@section('title', 'System Diagnostic')

@section('content')

    {{-- ── Header ─────────────────────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-2xl border border-rim-light px-7 py-6 mb-6 flex items-center justify-between gap-4"
        style="background: linear-gradient(135deg, #0d1a31 0%, #091220 100%)">
        <div class="absolute top-[-60px] right-[80px] w-[220px] h-[220px] rounded-full pointer-events-none"
            style="background: radial-gradient(circle, rgba(0,229,160,0.10) 0%, transparent 70%)"></div>

        <div>
            <div class="flex items-center gap-2.5 mb-1">
                <span class="text-[22px]">🩺</span>
                <h1 class="text-[20px] font-extrabold text-ink-primary">System Diagnostic</h1>
            </div>
            <p class="text-[13px] text-ink-muted">Health check semua layanan: Database, Cache, Object Storage.</p>
        </div>

        {{-- Overall badge --}}
        @if ($overall === 'ok')
            <div
                class="flex items-center gap-2 bg-accent-green/10 border border-accent-green/30 text-accent-green rounded-xl px-4 py-2 flex-shrink-0">
                <span class="w-2.5 h-2.5 rounded-full bg-accent-green animate-pulse"></span>
                <span class="font-space text-[12px] font-bold tracking-widest uppercase">All Systems Operational</span>
            </div>
        @elseif ($overall === 'warn')
            <div
                class="flex items-center gap-2 bg-accent-orange/10 border border-accent-orange/30 text-accent-orange rounded-xl px-4 py-2 flex-shrink-0">
                <span class="w-2.5 h-2.5 rounded-full bg-accent-orange animate-pulse"></span>
                <span class="font-space text-[12px] font-bold tracking-widest uppercase">Partial Degradation</span>
            </div>
        @else
            <div
                class="flex items-center gap-2 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl px-4 py-2 flex-shrink-0">
                <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                <span class="font-space text-[12px] font-bold tracking-widest uppercase">System Error Detected</span>
            </div>
        @endif
    </div>

    {{-- ── Summary stat bar ────────────────────────────────────────── --}}
    @php
        $okCount = collect($checks)->where('status', 'ok')->count();
        $warnCount = collect($checks)->where('status', 'warn')->count();
        $errCount = collect($checks)->where('status', 'error')->count();
        $total = count($checks);
    @endphp
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-card border border-accent-green/30 rounded-2xl px-5 py-4 text-center">
            <div class="text-[30px] font-extrabold text-accent-green">{{ $okCount }}</div>
            <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mt-1">✓ Passed</div>
        </div>
        <div class="bg-card border border-accent-orange/30 rounded-2xl px-5 py-4 text-center">
            <div class="text-[30px] font-extrabold text-accent-orange">{{ $warnCount }}</div>
            <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mt-1">⚠ Warning</div>
        </div>
        <div class="bg-card border border-red-500/30 rounded-2xl px-5 py-4 text-center">
            <div class="text-[30px] font-extrabold text-red-400">{{ $errCount }}</div>
            <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mt-1">✕ Failed</div>
        </div>
    </div>

    {{-- ── Check list ───────────────────────────────────────────────── --}}
    <div class="bg-card border border-rim rounded-2xl overflow-hidden mb-6">
        <div class="flex items-center justify-between px-[22px] py-4 border-b border-rim">
            <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase">🩺 Hasil Health Check</div>
            <div class="font-space text-[11px] text-ink-dim">{{ $okCount }}/{{ $total }} checks passed</div>
        </div>

        <div class="divide-y divide-rim">
            @foreach ($checks as $key => $check)
                <div class="flex items-start gap-4 px-[22px] py-4 hover:bg-white/[0.01] transition-colors">
                    {{-- Icon --}}
                    <div class="flex-shrink-0 mt-0.5">
                        @if ($check['status'] === 'ok')
                            <div class="w-8 h-8 rounded-lg bg-accent-green/15 flex items-center justify-center">
                                <span class="text-accent-green text-[16px]">✓</span>
                            </div>
                        @elseif ($check['status'] === 'warn')
                            <div class="w-8 h-8 rounded-lg bg-accent-orange/15 flex items-center justify-center">
                                <span class="text-accent-orange text-[15px]">⚠</span>
                            </div>
                        @else
                            <div class="w-8 h-8 rounded-lg bg-red-500/15 flex items-center justify-center">
                                <span class="text-red-400 text-[16px]">✕</span>
                            </div>
                        @endif
                    </div>

                    {{-- Label + detail --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2.5 mb-0.5">
                            <span class="text-[14px] font-bold text-ink-primary">{{ $check['label'] }}</span>
                            @if ($check['status'] === 'ok')
                                <span
                                    class="font-space text-[9px] bg-accent-green/10 border border-accent-green/25 text-accent-green rounded-full px-2 py-[2px] tracking-widest uppercase">OK</span>
                            @elseif ($check['status'] === 'warn')
                                <span
                                    class="font-space text-[9px] bg-accent-orange/10 border border-accent-orange/25 text-accent-orange rounded-full px-2 py-[2px] tracking-widest uppercase">WARN</span>
                            @else
                                <span
                                    class="font-space text-[9px] bg-red-500/10 border border-red-500/25 text-red-400 rounded-full px-2 py-[2px] tracking-widest uppercase">ERROR</span>
                            @endif
                        </div>
                        <p class="font-space text-[11px] text-ink-muted leading-relaxed">{{ $check['detail'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── MiniStack info box ───────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Endpoint info --}}
        <div class="bg-card border border-rim rounded-2xl p-5">
            <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mb-4">⚡ Konfigurasi Object Storage
            </div>
            @foreach ([['label' => 'Endpoint URL', 'value' => $url], ['label' => 'Region', 'value' => env('MINISTACK_REGION', 'us-east-1')], ['label' => 'Access Key', 'value' => env('MINISTACK_KEY', 'test')], ['label' => 'Path Style', 'value' => env('MINISTACK_PATH_STYLE', 'true')]] as $info)
                <div class="flex items-center gap-3 mb-2.5 last:mb-0">
                    <div class="text-[12px] text-ink-muted w-[110px] flex-shrink-0">{{ $info['label'] }}</div>
                    <div
                        class="font-space text-[12px] text-accent-bright bg-accent/5 border border-accent/15 rounded-lg px-3 py-1.5 flex-1 overflow-hidden text-ellipsis whitespace-nowrap">
                        {{ $info['value'] }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Tips --}}
        <div class="bg-card border border-rim rounded-2xl p-5">
            <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mb-4">💡 Tips & Troubleshoot</div>
            <div class="space-y-3 text-[12px] text-ink-muted leading-relaxed">
                <div class="flex items-start gap-2">
                    <span class="text-accent-green flex-shrink-0 mt-0.5">▶</span>
                    <span>Jika MiniStack <strong class="text-ink-primary">ERROR</strong>, pastikan ministack berjalan:
                        <code class="font-space text-[10px] bg-field border border-rim rounded px-1.5 py-0.5 ml-1">docker
                            run -p 4566:4566 ministack/ministack</code>
                    </span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-accent-green flex-shrink-0 mt-0.5">▶</span>
                    <span>Alternatif pakai <strong class="text-ink-primary">MinIO</strong>:
                        <code
                            class="font-space text-[10px] bg-field border border-rim rounded px-1.5 py-0.5 ml-1">MINISTACK_URL=http://localhost:9000</code>
                    </span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-accent-green flex-shrink-0 mt-0.5">▶</span>
                    <span>Jika <strong class="text-ink-primary">WARN</strong> di List Objects, XML response mungkin kosong —
                        ini normal untuk bucket baru.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-accent-green flex-shrink-0 mt-0.5">▶</span>
                    <span>Cek log detail di <code
                            class="font-space text-[10px] bg-field border border-rim rounded px-1.5 py-0.5">storage/logs/laravel.log</code></span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Refresh button ───────────────────────────────────────────── --}}
    <div class="mt-5 text-center">
        <a href="{{ route('diagnostic.index') }}" class="btn btn-outline">
            🔄 Jalankan Ulang Health Check
        </a>
    </div>

@endsection
