@extends('layouts.app')
@section('title', 'Log Aktivitas')

@section('content')

    {{-- ── Header ──────────────────────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-2xl border border-rim-light px-7 py-6 mb-6 flex items-center justify-between gap-4"
        style="background: linear-gradient(135deg, #0d1a31 0%, #091220 100%)">
        <div class="absolute top-[-60px] right-[80px] w-[200px] h-[200px] rounded-full pointer-events-none"
            style="background: radial-gradient(circle, rgba(167,139,250,0.12) 0%, transparent 70%)"></div>

        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[22px]">📋</span>
                <h1 class="text-[20px] font-extrabold text-ink-primary">Log Aktivitas</h1>
            </div>
            <p class="text-[13px] text-ink-muted">Pantau semua aktivitas dan perubahan pada resource Anda.</p>
        </div>
    </div>

    {{-- ── Log List ────────────────────────────────────────────────── --}}
    <div class="bg-card border border-rim rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-[22px] py-4 border-b border-rim">
            <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase">Riwayat Aktivitas Terbaru</div>
            <span class="font-space text-[11px] text-ink-dim">{{ $logs->total() }} record</span>
        </div>

        @if ($logs->count() > 0)
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Resource</th>
                            <th>Aksi</th>
                            <th>Status</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td class="text-[12px] text-ink-muted whitespace-nowrap">
                                    {{ $log->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="font-bold text-ink-primary">
                                    <div class="flex items-center gap-2">
                                        @php
                                            $icon = '📦';
                                            if ($log->resource_type === 'Storage') $icon = '🪣';
                                            if ($log->resource_type === 'Compute') $icon = '⚙';
                                            if ($log->resource_type === 'Database') $icon = '🗄';
                                            if ($log->resource_type === 'Credential') $icon = '🔑';
                                        @endphp
                                        <div class="w-6 h-6 rounded bg-accent/10 flex items-center justify-center text-[11px]">{{ $icon }}</div>
                                        <span>{{ $log->resource_name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td><span class="font-space text-[11px] text-ink-secondary">{{ $log->action }}</span></td>
                                <td>
                                    @if ($log->status === 'success')
                                        <span class="badge badge-green">Success</span>
                                    @elseif ($log->status === 'error')
                                        <span class="badge badge-red">Error</span>
                                    @else
                                        <span class="badge">{{ $log->status }}</span>
                                    @endif
                                </td>
                                <td class="font-space text-[11px] text-ink-dim">{{ $log->ip_address ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if ($logs->hasPages())
                <div class="px-5 py-4 border-t border-rim flex justify-center">
                    {{ $logs->links('pagination::tailwind') }}
                </div>
            @endif
        @else
            <div class="py-16 text-center">
                <div class="text-[48px] mb-3">📋</div>
                <div class="text-[15px] font-bold text-ink-primary mb-1.5">Belum Ada Aktivitas</div>
                <div class="text-[13px] text-ink-dim">Aktivitas resource Anda akan muncul di sini.</div>
            </div>
        @endif
    </div>

@endsection
