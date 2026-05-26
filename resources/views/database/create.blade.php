@extends('layouts.app')
@section('title', 'Buat Database')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('database.index') }}" class="text-[13px] text-ink-muted hover:text-ink-primary flex items-center gap-1.5 mb-3">
            ← Kembali ke Database
        </a>
        <h1 class="text-[22px] font-extrabold text-ink-primary">Buat Database Baru</h1>
        <p class="text-[13px] text-ink-muted mt-1">Managed database siap pakai dalam hitungan detik.</p>
    </div>

    <form action="{{ route('database.store') }}" method="POST">
        @csrf

        {{-- Nama & DB Name --}}
        <div class="bg-card border border-rim rounded-2xl p-5 mb-4">
            <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mb-4">1. Identifikasi</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[13px] font-semibold text-ink-primary mb-2">Nama Instance</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full bg-field border border-rim rounded-xl px-4 py-2.5 text-ink-primary text-[14px] focus:outline-none focus:border-accent transition-colors"
                        placeholder="misal: db-production" pattern="[a-zA-Z0-9\-_]+" required>
                    <p class="text-[11px] text-ink-muted mt-1.5">Label untuk identifikasi instance.</p>
                    @error('name')<p class="text-[12px] text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-ink-primary mb-2">Nama Database</label>
                    <input type="text" name="db_name" value="{{ old('db_name') }}"
                        class="w-full bg-field border border-rim rounded-xl px-4 py-2.5 text-ink-primary text-[14px] focus:outline-none focus:border-accent transition-colors"
                        placeholder="misal: myapp_db" pattern="[a-zA-Z0-9_]+" required>
                    <p class="text-[11px] text-ink-muted mt-1.5">Nama schema yang akan dibuat.</p>
                    @error('db_name')<p class="text-[12px] text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Engine --}}
        <div class="bg-card border border-rim rounded-2xl p-5 mb-4">
            <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mb-4">2. Pilih Database Engine</div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach ($engines as $eng)
                    <label class="cursor-pointer">
                        <input type="radio" name="engine" value="{{ $eng['id'] }}" class="hidden peer"
                            {{ old('engine', 'mysql-8.0') === $eng['id'] ? 'checked' : '' }}>
                        <div class="bg-field border border-rim rounded-xl px-4 py-3.5 peer-checked:border-accent peer-checked:bg-accent/5 transition-all flex items-start gap-3">
                            <span class="text-[24px] flex-shrink-0">{{ $eng['icon'] }}</span>
                            <div>
                                <div class="text-[13px] font-bold text-ink-primary">{{ $eng['name'] }}</div>
                                <div class="text-[11px] text-ink-dim mt-0.5">{{ $eng['desc'] }}</div>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('engine')<p class="text-[12px] text-red-400 mt-2">{{ $message }}</p>@enderror
        </div>

        {{-- Size --}}
        <div class="bg-card border border-rim rounded-2xl p-5 mb-4">
            <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mb-4">3. Pilih Ukuran Instance</div>
            <div class="grid grid-cols-1 gap-2.5">
                @foreach ($sizes as $size)
                    <label class="cursor-pointer">
                        <input type="radio" name="db_size" value="{{ $size['id'] }}" class="hidden peer"
                            {{ old('db_size', 'db.nano') === $size['id'] ? 'checked' : '' }}>
                        <div class="bg-field border border-rim rounded-xl px-4 py-3 flex items-center justify-between peer-checked:border-accent peer-checked:bg-accent/5 transition-all">
                            <div class="flex items-center gap-4">
                                <div class="w-2 h-2 rounded-full border-2 border-rim peer-checked:border-accent bg-transparent flex-shrink-0"></div>
                                <div>
                                    <span class="text-[14px] font-bold text-ink-primary">{{ $size['name'] }}</span>
                                    <span class="font-space text-[11px] text-ink-muted ml-3">{{ $size['vcpu'] }} vCPU · {{ $size['ram'] }} GB RAM · {{ $size['storage'] }} GB SSD</span>
                                </div>
                            </div>
                            <div class="font-space text-[12px] {{ $size['price'] === 0 ? 'text-accent-green' : 'text-accent-bright' }} font-bold flex-shrink-0">
                                {{ $size['price'] === 0 ? 'GRATIS' : 'Rp ' . number_format($size['price'], 0, ',', '.') . '/bln' }}
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('db_size')<p class="text-[12px] text-red-400 mt-2">{{ $message }}</p>@enderror
        </div>

        {{-- Credentials --}}
        <div class="bg-card border border-rim rounded-2xl p-5 mb-6">
            <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mb-4">4. Kredensial Database</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[13px] font-semibold text-ink-primary mb-2">Username</label>
                    <input type="text" name="db_user" value="{{ old('db_user', 'admin') }}"
                        class="w-full bg-field border border-rim rounded-xl px-4 py-2.5 text-ink-primary text-[14px] focus:outline-none focus:border-accent transition-colors"
                        placeholder="admin" required>
                    @error('db_user')<p class="text-[12px] text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-ink-primary mb-2">Password</label>
                    <input type="password" name="db_password"
                        class="w-full bg-field border border-rim rounded-xl px-4 py-2.5 text-ink-primary text-[14px] focus:outline-none focus:border-accent transition-colors"
                        placeholder="Min. 8 karakter" minlength="8" required>
                    @error('db_password')<p class="text-[12px] text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="mt-3 flex items-start gap-2 bg-accent/5 border border-accent/20 rounded-xl px-4 py-3">
                <span class="text-[15px] flex-shrink-0 mt-0.5">🔒</span>
                <p class="text-[12px] text-ink-muted">Password disimpan secara terenkripsi. Simpan di tempat yang aman — kami tidak bisa menampilkannya lagi setelah ini.</p>
            </div>
        </div>

        {{-- Tombol --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('database.index') }}" class="btn btn-outline px-5">Batal</a>
            <button type="submit" class="btn btn-primary px-8">🗄 Buat Database</button>
        </div>
    </form>
</div>
@endsection
