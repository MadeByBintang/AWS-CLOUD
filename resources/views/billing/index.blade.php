@extends('layouts.app')
@section('title', 'Billing & Tagihan')

@section('content')

    {{-- ── Header ──────────────────────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-2xl border border-rim-light px-7 py-6 mb-6 flex items-center justify-between gap-4"
        style="background: linear-gradient(135deg, #0d1a31 0%, #091220 100%)">
        <div class="absolute top-[-60px] right-[80px] w-[200px] h-[200px] rounded-full pointer-events-none"
            style="background: radial-gradient(circle, rgba(16,185,129,0.12) 0%, transparent 70%)"></div>

        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[22px]">💳</span>
                <h1 class="text-[20px] font-extrabold text-ink-primary">Billing & Tagihan</h1>
            </div>
            <p class="text-[13px] text-ink-muted">Kelola langganan dan riwayat transaksi Anda.</p>
        </div>

        <a href="{{ route('subscriptions.index') }}" class="btn btn-primary flex-shrink-0">
            🚀 Upgrade Paket
        </a>
    </div>

    {{-- ── Upgrade Banner (hanya jika paket Free) ─────────────────── --}}
    @if(($storageSub->plan ?? 'free') === 'free')
    <div class="relative overflow-hidden rounded-2xl border border-accent/30 px-6 py-4 mb-6 flex items-center justify-between gap-4"
        style="background: linear-gradient(135deg, rgba(26,108,246,0.12) 0%, rgba(0,212,255,0.05) 100%)">
        <div class="flex items-center gap-4">
            <div class="text-[28px]">⚡</div>
            <div>
                <div class="text-[14px] font-bold text-ink-primary">Anda menggunakan paket Free</div>
                <div class="text-[12px] text-ink-muted mt-0.5">Upgrade ke Pro (Rp 54.999/bln) untuk 15 GB storage & 10 bucket, atau Business (Rp 119.999/bln) untuk fitur enterprise.</div>
            </div>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('subscriptions.checkout', 'starter') }}" class="btn btn-primary text-[12px] py-2 px-4">Upgrade Pro →</a>
            <a href="{{ route('subscriptions.checkout', 'pro') }}" class="btn text-[12px] py-2 px-4" style="background:rgba(139,92,246,0.15);border-color:rgba(139,92,246,0.4);color:#a78bfa;">Upgrade Business →</a>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- ── Ringkasan Tagihan Aktif ─────────────────────────────── --}}
        <div class="bg-card border border-rim rounded-2xl overflow-hidden lg:col-span-1">
            <div class="px-5 py-4 border-b border-rim/60" style="background: linear-gradient(135deg, #0d1a31 0%, #091220 100%)">
                <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mb-1">Tagihan Bulan Ini</div>
                <div class="text-[32px] font-extrabold text-ink-primary">
                    <span class="text-[16px] text-ink-dim font-normal align-top mr-1">Rp</span>
                    {{ number_format($monthlyTotal ?? 0, 0, ',', '.') }}
                </div>
                @if(($monthlyTotal ?? 0) > 0)
                    <div class="text-[12px] text-ink-muted mt-1">Jatuh tempo <strong class="text-ink-secondary">{{ now()->endOfMonth()->format('d M Y') }}</strong></div>
                @else
                    <div class="text-[12px] text-accent-green mt-1 font-semibold">✓ Tidak ada tagihan aktif</div>
                @endif
            </div>

            <div class="px-5 py-4 space-y-3">
                {{-- Storage Sub --}}
                <div class="flex items-center justify-between py-2 border-b border-rim/50">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-accent/10 flex items-center justify-center text-[13px]">🗄</div>
                        <div>
                            <div class="text-[13px] font-semibold text-ink-primary">Storage</div>
                            <div class="text-[11px] text-ink-dim">Paket {{ $storageSub->displayName() }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-space text-[13px] font-bold text-ink-primary">Rp {{ number_format($storageSub->price ?? 0, 0, ',', '.') }}</div>
                        @if($storageSub->is_active ?? false)
                            <span class="text-[10px] text-accent-green">● Aktif</span>
                        @endif
                    </div>
                </div>

                {{-- Compute Sub --}}
                <div class="flex items-center justify-between py-2 border-b border-rim/50">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-accent-green/10 flex items-center justify-center text-[13px]">⚙</div>
                        <div>
                            <div class="text-[13px] font-semibold text-ink-primary">Compute</div>
                            <div class="text-[11px] text-ink-dim">Paket {{ ucfirst($computeSub->plan ?? 'Free') }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-space text-[13px] font-bold text-ink-primary">Rp {{ number_format($computeSub->price ?? 0, 0, ',', '.') }}</div>
                        @if($computeSub->is_active ?? false)
                            <span class="text-[10px] text-accent-green">● Aktif</span>
                        @endif
                    </div>
                </div>

                {{-- Total --}}
                <div class="flex items-center justify-between pt-1">
                    <span class="text-[13px] font-bold text-ink-primary">Total</span>
                    <span class="font-space text-[15px] font-extrabold text-accent-bright">Rp {{ number_format($monthlyTotal ?? 0, 0, ',', '.') }}</span>
                </div>

                @if(($monthlyTotal ?? 0) > 0)
                    <a href="{{ route('subscriptions.index') }}" class="btn btn-outline w-full text-center mt-2 block">Kelola Langganan</a>
                @else
                    <a href="{{ route('subscriptions.index') }}" class="btn btn-primary w-full text-center mt-2 block">🚀 Upgrade Sekarang</a>
                @endif
            </div>
        </div>

        {{-- ── Kanan: Metode Pembayaran + Riwayat ─────────────────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Info Paket Aktif --}}
            <div class="bg-card border border-rim rounded-2xl p-5">
                <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mb-4">Langganan Aktif</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    {{-- Storage Plan --}}
                    <div class="border border-rim rounded-xl px-4 py-3.5 flex items-center justify-between
                        @if(($storageSub->plan ?? 'free') !== 'free') border-accent/30 bg-accent/[0.04] @endif">
                        <div class="flex items-center gap-3">
                            <span class="text-[18px]">
                                @if(($storageSub->plan ?? 'free') === 'free') 🌱
                                @elseif(($storageSub->plan ?? '') === 'starter') 🚀
                                @else 🏢 @endif
                            </span>
                            <div>
                                <div class="text-[13px] font-bold text-ink-primary">Storage {{ $storageSub->displayName() }}</div>
                                <div class="text-[11px] text-ink-dim">{{ $storageSub->quota_gb ?? 5 }} GB · {{ $storageSub->bucket_limit >= 9999 ? 'Unlimited' : $storageSub->bucket_limit }} Bucket</div>
                            </div>
                        </div>
                        @if(($storageSub->plan ?? 'free') === 'free')
                            <a href="{{ route('subscriptions.checkout', 'starter') }}" class="text-[11px] text-accent-bright font-bold hover:underline no-underline">Upgrade</a>
                        @else
                            <span class="badge badge-green">Aktif</span>
                        @endif
                    </div>

                    {{-- Compute Plan --}}
                    <div class="border border-rim rounded-xl px-4 py-3.5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-[18px]">⚙</span>
                            <div>
                                <div class="text-[13px] font-bold text-ink-primary">Compute {{ ucfirst($computeSub->plan ?? 'Free') }}</div>
                                <div class="text-[11px] text-ink-dim">{{ $computeSub->vcpu_limit ?? 1 }} vCPU · {{ $computeSub->compute_units ?? 100 }} CU/bln</div>
                            </div>
                        </div>
                        <span class="badge badge-green">Aktif</span>
                    </div>
                </div>
            </div>

            {{-- Riwayat Invoice --}}
            <div class="bg-card border border-rim rounded-2xl overflow-hidden">
                <div class="px-[22px] py-4 border-b border-rim flex items-center justify-between">
                    <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase">Riwayat Invoice</div>
                    <span class="text-[11px] text-ink-dim">{{ $invoices->count() }} transaksi</span>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Tanggal</th>
                                <th>Paket</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($invoices as $inv)
                                <tr>
                                    <td class="font-space text-[12px] text-accent-bright">{{ $inv['id'] }}</td>
                                    <td class="text-[12px] text-ink-muted">{{ $inv['date']->format('d M Y') }}</td>
                                    <td class="text-[12px] text-ink-primary font-semibold">{{ $inv['plan'] }}</td>
                                    <td class="font-space text-[12px] font-bold">
                                        @if($inv['amount'] == 0)
                                            <span class="text-ink-dim">Gratis</span>
                                        @else
                                            Rp {{ number_format($inv['amount'], 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($inv['status'] === 'Paid')
                                            <span class="badge badge-green">Paid</span>
                                        @elseif($inv['status'] === 'Free')
                                            <span class="badge" style="background:rgba(100,116,139,0.15);color:#94a3b8;border:1px solid rgba(100,116,139,0.3)">Free</span>
                                        @else
                                            <span class="badge" style="background:rgba(239,68,68,0.12);color:#f87171;border:1px solid rgba(239,68,68,0.25)">Expired</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-ink-dim text-[13px]">Belum ada riwayat transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

@endsection
