@extends('layouts.app')
@section('title', 'Pengaturan')

@section('content')

    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-[20px] font-extrabold text-ink-primary">⚙ Pengaturan Sistem</h1>
            <p class="text-[13px] text-ink-muted">Konfigurasi preferensi akun dan batasan keamanan.</p>
        </div>

        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            {{-- Preferensi Notifikasi --}}
            <div class="bg-card border border-rim rounded-2xl p-5 mb-5">
                <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mb-4">Preferensi Notifikasi</div>
                
                <div class="space-y-4">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="notif_billing" class="mt-1" checked>
                        <div>
                            <div class="text-[13px] font-bold text-ink-primary">Tagihan & Pembayaran</div>
                            <div class="text-[12px] text-ink-muted">Email setiap kali invoice diterbitkan atau pembayaran berhasil.</div>
                        </div>
                    </label>
                    
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="notif_security" class="mt-1" checked>
                        <div>
                            <div class="text-[13px] font-bold text-ink-primary">Peringatan Keamanan</div>
                            <div class="text-[12px] text-ink-muted">Email ketika ada login dari perangkat/lokasi baru.</div>
                        </div>
                    </label>
                    
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="notif_quota" class="mt-1" checked>
                        <div>
                            <div class="text-[13px] font-bold text-ink-primary">Peringatan Kuota</div>
                            <div class="text-[12px] text-ink-muted">Beritahu saya saat penggunaan storage/compute mencapai 80% dari batas paket.</div>
                        </div>
                    </label>
                </div>
            </div>
            
            {{-- Keamanan Tambahan --}}
            <div class="bg-card border border-rim rounded-2xl p-5 mb-5">
                <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mb-4">Keamanan Lanjutan</div>
                
                <div class="flex items-center justify-between p-4 bg-field border border-rim rounded-xl mb-3">
                    <div>
                        <div class="text-[13px] font-bold text-ink-primary flex items-center gap-2">
                            <span>📱</span> Autentikasi Dua Faktor (2FA)
                            <span class="badge badge-orange text-[9px] ml-2">BELUM AKTIF</span>
                        </div>
                        <div class="text-[12px] text-ink-muted mt-1">Gunakan aplikasi authenticator untuk keamanan ekstra.</div>
                    </div>
                    <button type="button" class="btn btn-outline text-[12px]">Aktifkan</button>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn btn-primary px-6">Simpan Pengaturan</button>
            </div>
        </form>
    </div>

@endsection
