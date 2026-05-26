@extends('layouts.app')
@section('title', 'Compute')

@section('content')

    {{-- ── Header ──────────────────────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-2xl border border-rim-light px-7 py-6 mb-6 flex items-center justify-between gap-4"
        style="background: linear-gradient(135deg, #0d1a31 0%, #091220 100%)">
        <div class="absolute top-[-60px] right-[80px] w-[200px] h-[200px] rounded-full pointer-events-none"
            style="background: radial-gradient(circle, rgba(167,139,250,0.12) 0%, transparent 70%)"></div>

        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[22px]">⚙</span>
                <h1 class="text-[20px] font-extrabold text-ink-primary">Compute Instances</h1>
            </div>
            <p class="text-[13px] text-ink-muted">Kelola virtual machine dan container Anda.</p>
        </div>

        <a href="{{ route('compute.create') }}" class="btn btn-primary flex-shrink-0">
            + Launch Instance
        </a>
    </div>

    {{-- ── Stat Cards ───────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @foreach ([
            ['label' => 'Instance Running', 'value' => $runningCount,          'icon' => '🟢', 'color' => 'accent-green'],
            ['label' => 'Instance Stopped', 'value' => $stoppedCount,          'icon' => '🔴', 'color' => 'accent-red'],
            ['label' => 'vCPU Aktif',       'value' => $totalVcpu . ' vCPU',   'icon' => '⚡', 'color' => 'accent'],
            ['label' => 'RAM Aktif',         'value' => $totalRam . ' GB',      'icon' => '💾', 'color' => 'accent-cyan'],
        ] as $stat)
            <div class="bg-card border border-rim rounded-2xl px-5 py-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase">{{ $stat['label'] }}</div>
                    <span class="text-[18px]">{{ $stat['icon'] }}</span>
                </div>
                <div class="text-[26px] font-extrabold text-ink-primary">{{ $stat['value'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ── Paket Compute ────────────────────────────────────────────── --}}
    <div class="bg-card border border-rim rounded-2xl px-5 py-4 mb-6 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-accent-purple/20 flex items-center justify-center text-[18px]">📦</div>
            <div>
                <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mb-0.5">Paket Compute Aktif</div>
                <div class="text-[15px] font-bold text-ink-primary">{{ ucfirst($computeSub->plan) }}
                    <span class="badge badge-green ml-2">● Aktif</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-6 text-center">
            <div>
                <div class="font-space text-[10px] text-ink-muted mb-0.5">vCPU Limit</div>
                <div class="text-[18px] font-bold text-accent-cyan">{{ $computeSub->vcpu_limit }}</div>
            </div>
            <div>
                <div class="font-space text-[10px] text-ink-muted mb-0.5">RAM Limit</div>
                <div class="text-[18px] font-bold text-accent-cyan">{{ $computeSub->ram_go }} GB</div>
            </div>
            <div>
                <div class="font-space text-[10px] text-ink-muted mb-0.5">Compute Units</div>
                <div class="text-[18px] font-bold text-accent-cyan">{{ $computeSub->compute_units }}</div>
            </div>
            <a href="{{ route('subscriptions.index') }}" class="btn btn-outline text-[12px] py-[6px] px-3.5">Upgrade</a>
        </div>
    </div>

    {{-- ── Instance List ─────────────────────────────────────────────── --}}
    <div class="bg-card border border-rim rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-[22px] py-4 border-b border-rim">
            <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase">⚙ Daftar Instance</div>
            <span class="font-space text-[11px] text-ink-dim">{{ $instances->count() }} instance</span>
        </div>

        @if ($instances->count() > 0)
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Tipe</th>
                            <th>OS</th>
                            <th>vCPU / RAM</th>
                            <th>IP Address</th>
                            <th>Status</th>
                            <th>Uptime</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($instances as $inst)
                            <tr>
                                <td class="font-bold text-ink-primary">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-accent-purple/20 flex items-center justify-center text-[13px]">⚙</div>
                                        {{ $inst->name }}
                                    </div>
                                </td>
                                <td><span class="font-space text-[11px] text-accent-bright">{{ $inst->instance_type }}</span></td>
                                <td class="text-[12px] text-ink-muted">{{ $inst->os_image }}</td>
                                <td class="font-space text-[12px]">{{ $inst->vcpu }} vCPU / {{ $inst->ram_gb }} GB</td>
                                <td class="font-space text-[11px] text-accent-cyan">{{ $inst->ip_address ?? '—' }}</td>
                                <td>
                                    @if ($inst->status === 'running')
                                        <span class="badge badge-green">● Running</span>
                                    @elseif ($inst->status === 'stopped')
                                        <span class="badge badge-orange">● Stopped</span>
                                    @else
                                        <span class="badge">{{ $inst->status }}</span>
                                    @endif
                                </td>
                                <td class="text-[12px] text-ink-muted">
                                    {{ $inst->started_at?->diffForHumans() ?? '—' }}
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        {{-- Toggle Start/Stop --}}
                                        <form method="POST" action="{{ route('compute.toggle', $inst->id) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                class="btn text-[11px] py-1 px-2.5 {{ $inst->status === 'running' ? 'btn-outline' : 'btn-primary' }}">
                                                {{ $inst->status === 'running' ? '⏸ Stop' : '▶ Start' }}
                                            </button>
                                        </form>
                                        {{-- Terminate --}}
                                        <form method="POST" action="{{ route('compute.destroy', $inst->id) }}"
                                            onsubmit="return confirm('Hapus instance ini secara permanen?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn text-[11px] py-1 px-2.5"
                                                style="background:rgba(239,68,68,0.08);border-color:rgba(239,68,68,0.25);color:#f87171">
                                                🗑
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-16 text-center">
                <div class="text-[48px] mb-3">⚙</div>
                <div class="text-[15px] font-bold text-ink-primary mb-1.5">Belum Ada Instance</div>
                <div class="text-[13px] text-ink-dim mb-5">Launch virtual machine pertama Anda untuk mulai komputasi.</div>
                <a href="{{ route('compute.create') }}" class="btn btn-primary">+ Launch Instance Pertama</a>
            </div>
        @endif
    </div>

@endsection
