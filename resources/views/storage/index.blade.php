@extends('layouts.app')
@section('title', 'Storage Bucket')

@section('content')

    {{-- ── Header ──────────────────────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-2xl border border-rim-light px-7 py-6 mb-6 flex items-center justify-between gap-4"
        style="background: linear-gradient(135deg, #0d1a31 0%, #091220 100%)">
        <div class="absolute top-[-60px] right-[80px] w-[200px] h-[200px] rounded-full pointer-events-none"
            style="background: radial-gradient(circle, rgba(26,108,246,0.12) 0%, transparent 70%)"></div>

        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[22px]">🗄</span>
                <h1 class="text-[20px] font-extrabold text-ink-primary">Storage Bucket</h1>
            </div>
            <p class="text-[13px] text-ink-muted">Kelola object storage S3-compatible Anda.</p>
        </div>

        <a href="{{ route('storage.create') }}" class="btn btn-primary flex-shrink-0">
            + Buat Bucket
        </a>
    </div>

    {{-- ── Stat Cards ──────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @foreach ([
            ['label' => 'Total Bucket',   'value' => $buckets->count(),                                                  'icon' => '🪣'],
            ['label' => 'Total Objek',    'value' => $buckets->sum('object_count'),                                      'icon' => '📄'],
            ['label' => 'Storage Terpakai','value' => round($buckets->sum('size_bytes') / 1073741824, 2) . ' GB',        'icon' => '💾'],
            ['label' => 'Kuota Tersisa',  'value' => ($storageSub->quota_gb - round($buckets->sum('size_bytes')/1073741824,2)) . ' GB', 'icon' => '📊'],
        ] as $stat)
            <div class="bg-card border border-rim rounded-2xl px-5 py-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase">{{ $stat['label'] }}</div>
                    <span class="text-[18px]">{{ $stat['icon'] }}</span>
                </div>
                <div class="text-[24px] font-extrabold text-ink-primary">{{ $stat['value'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ── Plan Info ────────────────────────────────────────────────── --}}
    <div class="bg-card border border-rim rounded-2xl px-5 py-4 mb-6 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-accent/20 flex items-center justify-center text-[18px]">📦</div>
            <div>
                <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mb-0.5">Paket Storage Aktif</div>
                <div class="text-[15px] font-bold text-ink-primary">{{ $storageSub->displayName() }}
                    <span class="badge badge-green ml-2">● Aktif</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-6 text-center">
            <div>
                <div class="font-space text-[10px] text-ink-muted mb-0.5">Bucket Limit</div>
                <div class="text-[18px] font-bold
                    @if($buckets->count() >= $storageSub->bucket_limit) text-red-400 @else text-accent-cyan @endif">
                    {{ $buckets->count() }} / {{ $storageSub->bucket_limit >= 9999 ? '∞' : $storageSub->bucket_limit }}
                </div>
            </div>
            <div>
                <div class="font-space text-[10px] text-ink-muted mb-0.5">Kuota</div>
                <div class="text-[18px] font-bold text-accent-cyan">{{ $storageSub->quota_gb }} GB</div>
            </div>
            @if(($storageSub->plan ?? 'free') === 'free')
                <a href="{{ route('subscriptions.checkout', 'starter') }}" class="btn btn-primary text-[12px] py-[6px] px-3.5">🚀 Upgrade Pro</a>
            @elseif(($storageSub->plan ?? '') === 'starter')
                <a href="{{ route('subscriptions.checkout', 'pro') }}" class="btn text-[12px] py-[6px] px-3.5" style="background:rgba(139,92,246,0.15);border-color:rgba(139,92,246,0.4);color:#a78bfa;">🏢 Upgrade Business</a>
            @else
                <a href="{{ route('billing.index') }}" class="btn btn-outline text-[12px] py-[6px] px-3.5">💳 Billing</a>
            @endif
        </div>
    </div>

    {{-- Upgrade alert jika bucket limit penuh --}}
    @if($buckets->count() >= $storageSub->bucket_limit)
    <div class="border border-red-500/30 bg-red-500/[0.08] rounded-2xl px-5 py-3 mb-6 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-[20px]">⚠️</span>
            <div>
                <div class="text-[13px] font-bold text-red-400">Batas Bucket Tercapai</div>
                <div class="text-[12px] text-ink-muted">Anda tidak bisa membuat bucket baru. Upgrade paket untuk menambah kapasitas.</div>
            </div>
        </div>
        <a href="{{ route('subscriptions.index') }}" class="btn btn-primary text-[12px] py-2 px-4 flex-shrink-0">Upgrade Sekarang</a>
    </div>
    @endif

    {{-- ── Bucket List ──────────────────────────────────────────────── --}}
    <div class="bg-card border border-rim rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-[22px] py-4 border-b border-rim">
            <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase">🪣 Daftar Bucket</div>
            <span class="font-space text-[11px] text-ink-dim">{{ $buckets->count() }} / {{ $storageSub->bucket_limit }} bucket</span>
        </div>

        @if ($buckets->count() > 0)
            <div class="grid grid-cols-[repeat(auto-fill,minmax(260px,1fr))] gap-3 p-5">
                @foreach ($buckets as $bucket)
                    <a href="{{ route('storage.show', $bucket->id) }}"
                        class="bg-field border border-rim rounded-[12px] px-4 py-4 flex items-start gap-3 no-underline transition-all hover:border-accent hover:bg-card-hover group">
                        <div class="w-10 h-10 rounded-xl bg-accent/[0.12] flex items-center justify-center text-[18px] flex-shrink-0 group-hover:scale-110 transition-transform">🪣</div>
                        <div class="overflow-hidden flex-1">
                            <div class="text-[14px] font-bold text-ink-primary whitespace-nowrap overflow-hidden text-ellipsis">
                                {{ $bucket->name }}
                            </div>
                            <div class="font-space text-[10px] text-ink-muted mt-1 flex items-center gap-2">
                                <span>{{ $bucket->object_count }} objek</span>
                                <span>·</span>
                                <span>{{ round($bucket->size_bytes / 1048576, 1) }} MB</span>
                            </div>
                            <div class="flex items-center gap-2 mt-2">
                                @if ($bucket->is_public)
                                    <span class="font-space text-[9px] bg-accent-orange/10 border border-accent-orange/30 text-accent-orange rounded-full px-2 py-[2px]">PUBLIC</span>
                                @else
                                    <span class="font-space text-[9px] bg-accent-green/10 border border-accent-green/30 text-accent-green rounded-full px-2 py-[2px]">PRIVATE</span>
                                @endif
                                @if ($bucket->versioning)
                                    <span class="font-space text-[9px] bg-accent/10 border border-accent/30 text-accent-bright rounded-full px-2 py-[2px]">VERSIONING</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach

                @if ($buckets->count() < $storageSub->bucket_limit)
                    <a href="{{ route('storage.create') }}"
                        class="bg-field border border-dashed border-rim rounded-[12px] px-4 py-4 flex flex-col items-center justify-center gap-2 no-underline transition-all hover:border-accent hover:bg-card-hover text-center min-h-[100px]">
                        <div class="text-[28px] text-ink-dim">+</div>
                        <div class="text-[13px] text-ink-dim">Buat Bucket Baru</div>
                    </a>
                @endif
            </div>
        @else
            <div class="py-16 text-center">
                <div class="text-[48px] mb-3">🪣</div>
                <div class="text-[15px] font-bold text-ink-primary mb-1.5">Belum Ada Bucket</div>
                <div class="text-[13px] text-ink-dim mb-5">Buat bucket pertama Anda untuk mulai menyimpan file.</div>
                <a href="{{ route('storage.create') }}" class="btn btn-primary">+ Buat Bucket Pertama</a>
            </div>
        @endif
    </div>

@endsection
