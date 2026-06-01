@extends('layouts.app')
@section('title', 'Profil Saya')

@section('content')

    <div class="max-w-2xl mx-auto">
        {{-- ── Header ──────────────────────────────────────────────────── --}}
        <div class="mb-6 flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-[18px] font-bold text-white flex-shrink-0"
                style="background: linear-gradient(135deg, #1a6cf6, #a78bfa)">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <h1 class="text-[20px] font-extrabold text-ink-primary">Profil Saya</h1>
                <p class="text-[13px] text-ink-muted">Kelola informasi personal dan kata sandi akun Anda.</p>
            </div>
        </div>

        <div class="bg-card border border-rim rounded-2xl p-6">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mb-4 border-b border-rim pb-2">Informasi Dasar</div>
                
                <div class="mb-4">
                    <label class="block text-[13px] font-semibold text-ink-primary mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full bg-field border border-rim rounded-xl px-4 py-2.5 text-ink-primary text-[14px] focus:outline-none focus:border-accent transition-colors" required>
                    @error('name')<p class="text-[12px] text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-6">
                    <label class="block text-[13px] font-semibold text-ink-primary mb-2">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full bg-field border border-rim rounded-xl px-4 py-2.5 text-ink-primary text-[14px] focus:outline-none focus:border-accent transition-colors" required>
                    @error('email')<p class="text-[12px] text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mb-4 border-b border-rim pb-2">Ubah Kata Sandi</div>
                <p class="text-[12px] text-ink-dim mb-4">Kosongkan jika tidak ingin mengubah kata sandi.</p>

                <div class="mb-4">
                    <label class="block text-[13px] font-semibold text-ink-primary mb-2">Kata Sandi Saat Ini</label>
                    <input type="password" name="current_password"
                        class="w-full bg-field border border-rim rounded-xl px-4 py-2.5 text-ink-primary text-[14px] focus:outline-none focus:border-accent transition-colors">
                    @error('current_password')<p class="text-[12px] text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-6">
                    <label class="block text-[13px] font-semibold text-ink-primary mb-2">Kata Sandi Baru</label>
                    <input type="password" name="new_password" minlength="8"
                        class="w-full bg-field border border-rim rounded-xl px-4 py-2.5 text-ink-primary text-[14px] focus:outline-none focus:border-accent transition-colors">
                    @error('new_password')<p class="text-[12px] text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn btn-primary px-6">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

@endsection
