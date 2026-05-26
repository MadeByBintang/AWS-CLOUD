@extends('layouts.app')
@section('title', 'Database (DBaaS)')

@section('content')

    {{-- ── Header ──────────────────────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-2xl border border-rim-light px-7 py-6 mb-6 flex items-center justify-between gap-4"
        style="background: linear-gradient(135deg, #0d1a31 0%, #091220 100%)">
        <div class="absolute top-[-60px] right-[80px] w-[200px] h-[200px] rounded-full pointer-events-none"
            style="background: radial-gradient(circle, rgba(0,229,160,0.10) 0%, transparent 70%)"></div>

        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[22px]">🗄</span>
                <h1 class="text-[20px] font-extrabold text-ink-primary">Database as a Service</h1>
            </div>
            <p class="text-[13px] text-ink-muted">MySQL, PostgreSQL, MariaDB & Redis — fully managed.</p>
        </div>

        <a href="{{ route('database.create') }}" class="btn btn-primary flex-shrink-0">
            + Buat Database
        </a>
    </div>

    {{-- ── Stat Cards ──────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @foreach ([
            ['label' => 'DB Available', 'value' => $runningCount,          'icon' => '🟢', 'sub' => 'instance'],
            ['label' => 'Total DB',     'value' => count($databases),       'icon' => '🗄',  'sub' => 'database'],
            ['label' => 'Storage Used', 'value' => $totalStorage . ' GB',  'icon' => '💾', 'sub' => 'total'],
            ['label' => 'Engine',       'value' => 'Multi',                 'icon' => '🔧', 'sub' => 'MySQL · PG · Redis'],
        ] as $stat)
            <div class="bg-card border border-rim rounded-2xl px-5 py-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase">{{ $stat['label'] }}</div>
                    <span class="text-[18px]">{{ $stat['icon'] }}</span>
                </div>
                <div class="text-[24px] font-extrabold text-ink-primary">{{ $stat['value'] }}</div>
                <div class="text-[11px] text-ink-dim mt-0.5">{{ $stat['sub'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ── Engine Chips ─────────────────────────────────────────────── --}}
    <div class="bg-card border border-rim rounded-2xl px-5 py-4 mb-6">
        <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mb-3">Engine yang Tersedia</div>
        <div class="flex flex-wrap gap-2">
            @foreach ([
                ['icon'=>'🐬','name'=>'MySQL 8.0','color'=>'#f97316'],
                ['icon'=>'🐘','name'=>'PostgreSQL 16','color'=>'#60a5fa'],
                ['icon'=>'🦭','name'=>'MariaDB 10.11','color'=>'#a78bfa'],
                ['icon'=>'🔴','name'=>'Redis 7','color'=>'#f87171'],
            ] as $eng)
                <div class="flex items-center gap-2 bg-field border border-rim rounded-full px-3.5 py-1.5 text-[12px] font-semibold text-ink-secondary">
                    <span>{{ $eng['icon'] }}</span> {{ $eng['name'] }}
                    <span class="font-space text-[9px] text-accent-green ml-1">✓ Ready</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Database List ────────────────────────────────────────────── --}}
    <div class="bg-card border border-rim rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-[22px] py-4 border-b border-rim">
            <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase">🗄 Daftar Database Instance</div>
            <span class="font-space text-[11px] text-ink-dim">{{ count($databases) }} database</span>
        </div>

        @if (count($databases) > 0)
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Engine</th>
                            <th>Ukuran</th>
                            <th>Storage</th>
                            <th>Endpoint</th>
                            <th>Port</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($databases as $db)
                            @php
                                $icons = ['mysql'=>'🐬','postgresql'=>'🐘','mariadb'=>'🦭','redis'=>'🔴'];
                                $icon = '🗄';
                                foreach ($icons as $k => $v) { if (str_contains($db['engine'], $k)) $icon = $v; }
                            @endphp
                            <tr>
                                <td class="font-bold text-ink-primary">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-accent-green/10 flex items-center justify-center text-[13px]">{{ $icon }}</div>
                                        {{ $db['name'] }}
                                    </div>
                                </td>
                                <td><span class="font-space text-[11px] text-accent-bright">{{ $db['engine'] }}</span></td>
                                <td class="font-space text-[12px]">{{ $db['vcpu'] }} vCPU · {{ $db['ram_gb'] }} GB</td>
                                <td class="font-space text-[12px]">{{ $db['storage_gb'] }} GB</td>
                                <td class="font-space text-[11px] text-accent-cyan">{{ $db['endpoint'] }}</td>
                                <td class="font-space text-[12px]">:{{ $db['port'] }}</td>
                                <td><span class="badge badge-green">● Available</span></td>
                                <td class="text-[12px] text-ink-muted">
                                    {{ \Carbon\Carbon::parse($db['created_at'])->diffForHumans() }}
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('database.destroy', $db['id']) }}"
                                        onsubmit="return confirm('Hapus database ini? Data akan hilang permanen.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn text-[11px] py-1 px-2.5"
                                            style="background:rgba(239,68,68,0.08);border-color:rgba(239,68,68,0.25);color:#f87171">
                                            🗑 Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-16 text-center">
                <div class="text-[48px] mb-3">🗄</div>
                <div class="text-[15px] font-bold text-ink-primary mb-1.5">Belum Ada Database</div>
                <div class="text-[13px] text-ink-dim mb-5">Buat managed database pertama Anda dalam hitungan detik.</div>
                <a href="{{ route('database.create') }}" class="btn btn-primary">+ Buat Database Pertama</a>
            </div>
        @endif
    </div>

@endsection
