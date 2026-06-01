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
            <p class="text-[13px] text-ink-muted">Kelola metode pembayaran dan riwayat transaksi Anda.</p>
        </div>
        
        <a href="{{ route('subscriptions.index') }}" class="btn btn-outline flex-shrink-0">
            Ubah Paket Langganan
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        {{-- Ringkasan Tagihan Aktif --}}
        <div class="bg-card border border-rim rounded-2xl p-5 lg:col-span-1">
            <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mb-4">Tagihan Bulan Ini</div>
            <div class="text-[32px] font-extrabold text-ink-primary mb-1">
                <span class="text-[16px] text-ink-dim font-normal align-top mr-1">Rp</span>{{ number_format(($storageSub->price ?? 0) + ($computeSub->price ?? 0), 0, ',', '.') }}
            </div>
            <div class="text-[12px] text-ink-muted mb-6">Jatuh tempo pada <strong class="text-ink-secondary">{{ now()->endOfMonth()->format('d M Y') }}</strong></div>
            
            <div class="space-y-3 mb-6">
                <div class="flex items-center justify-between pb-3 border-b border-rim">
                    <div class="flex items-center gap-2">
                        <span class="text-[14px]">🗄</span>
                        <span class="text-[13px] font-semibold">Storage ({{ ucfirst($storageSub->plan ?? 'Free') }})</span>
                    </div>
                    <span class="font-space text-[12px]">Rp {{ number_format($storageSub->price ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between pb-3 border-b border-rim">
                    <div class="flex items-center gap-2">
                        <span class="text-[14px]">⚙</span>
                        <span class="text-[13px] font-semibold">Compute ({{ ucfirst($computeSub->plan ?? 'Free') }})</span>
                    </div>
                    <span class="font-space text-[12px]">Rp {{ number_format($computeSub->price ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
            
            <button class="btn btn-primary w-full">Bayar Sekarang</button>
        </div>

        {{-- Metode Pembayaran & Riwayat --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Metode Pembayaran --}}
            <div class="bg-card border border-rim rounded-2xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase">Metode Pembayaran</div>
                    <button class="text-[12px] text-accent hover:underline">+ Tambah Metode</button>
                </div>
                
                <div class="bg-field border border-accent/30 rounded-xl px-4 py-3.5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-6 bg-white rounded flex items-center justify-center font-bold text-blue-900 text-[10px] italic">VISA</div>
                        <div>
                            <div class="text-[13px] font-bold text-ink-primary">Visa berakhiran 4242</div>
                            <div class="text-[11px] text-ink-muted">Kedaluwarsa 12/28</div>
                        </div>
                    </div>
                    <span class="badge badge-green">Utama</span>
                </div>
            </div>

            {{-- Riwayat Transaksi --}}
            <div class="bg-card border border-rim rounded-2xl overflow-hidden">
                <div class="px-[22px] py-4 border-b border-rim">
                    <div class="font-space text-[10px] text-ink-muted tracking-[1.5px] uppercase">Riwayat Invoice</div>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Tanggal</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $inv)
                                <tr>
                                    <td class="font-space text-[12px] text-accent-bright">{{ $inv['id'] }}</td>
                                    <td class="text-[12px] text-ink-muted">{{ $inv['date']->format('d M Y') }}</td>
                                    <td class="font-space text-[12px] font-bold">Rp {{ number_format($inv['amount'], 0, ',', '.') }}</td>
                                    <td><span class="badge badge-green">{{ $inv['status'] }}</span></td>
                                    <td class="text-right">
                                        <button class="text-[11px] text-ink-dim hover:text-ink-primary">Unduh PDF</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
