@extends('layouts.app')
@section('title', 'Buat Bucket Baru')

@section('content')
    <div class="max-w-xl mx-auto bg-card border border-rim rounded-2xl p-6 mt-10">
        <h2 class="text-xl font-bold text-ink-primary mb-2">Buat Bucket Storage Baru</h2>
        <p class="text-sm text-ink-muted mb-6">Bucket ini akan diisolasi dan disimpan secara aman di infrastruktur MiniStack.
        </p>

        <form action="{{ route('storage.store') }}" method="POST">
            @csrf

            <div class="mb-5">
                <label class="block text-[13px] font-semibold text-ink-primary mb-2">Nama Bucket</label>
                <input type="text" name="name"
                    class="w-full bg-field border border-rim rounded-lg px-4 py-2.5 text-ink-primary text-sm focus:outline-none focus:border-accent transition-colors"
                    placeholder="misal: backup-data-web" pattern="[a-z0-9\-]+"
                    title="Hanya huruf, angka, dan strip (-) yang diizinkan">
                <p class="text-[11px] text-ink-muted mt-2">Nama ini akan digabungkan dengan ID akun Anda secara otomatis.
                </p>
            </div>

            <div class="flex items-center gap-3 mt-8">
                <a href="{{ route('dashboard') }}" class="btn btn-outline text-sm px-5 py-2">Batal</a>
                <button type="submit" class="btn btn-primary text-sm px-5 py-2">Buat Sekarang</button>
            </div>
        </form>
    </div>
@endsection
