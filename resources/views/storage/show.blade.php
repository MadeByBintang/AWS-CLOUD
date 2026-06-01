@extends('layouts.app')
@section('title', 'Detail Bucket')

@section('content')
    <div class="bg-card border border-rim rounded-2xl overflow-hidden">

        <div class="flex items-center justify-between px-6 py-4 border-b border-rim">
            <div>
                <h2 class="text-[16px] font-bold text-ink-primary">🪣 {{ $bucket->name }}</h2>
                <p class="text-[12px] text-ink-muted mt-0.5">{{ $bucket->ministack_name }}</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline text-sm px-4 py-2">← Kembali</a>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div
                class="mx-6 mt-4 px-4 py-3 bg-accent-green/10 border border-accent-green/20 rounded-lg text-[13px] text-accent-green">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mx-6 mt-4 px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-lg text-[13px] text-red-400">
                {{ session('error') }}
            </div>
        @endif

        {{-- Form Upload --}}
        <div class="px-6 py-5 border-b border-rim">
            <form action="{{ route('storage.upload', $bucket->id) }}" method="POST" enctype="multipart/form-data"
                class="flex items-center gap-3">
                @csrf
                <input type="file" name="file" required
                    class="flex-1 text-[13px] text-ink-secondary bg-field border border-rim rounded-lg px-4 py-2
                       file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0
                       file:text-[12px] file:bg-accent/10 file:text-accent-bright cursor-pointer">
                <button type="submit" class="btn btn-primary text-sm px-5 py-2 flex-shrink-0">
                    Upload
                </button>
            </form>
            <p class="text-[11px] text-ink-muted mt-2">Maksimal ukuran file 50MB.</p>
        </div>

        {{-- Daftar File --}}
        <div class="px-6 py-5">
            @if (count($files) > 0)
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-ink-muted text-[11px] uppercase tracking-wider">
                            <th class="pb-3">Nama File</th>
                            <th class="pb-3">Ukuran</th>
                            <th class="pb-3">Terakhir Diubah</th>
                            <th class="pb-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($files as $file)
                            <tr class="border-t border-rim">
                                <td class="py-3 font-mono text-[12px] text-ink-primary">
                                    {{ $file['key'] }}
                                </td>
                                <td class="py-3 text-ink-muted text-[12px]">
                                    {{ number_format($file['size'] / 1024, 2) }} KB
                                </td>
                                <td class="py-3 text-ink-muted text-[12px]">
                                    {{ $file['last_modified'] }}
                                </td>
                                <td class="py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Download --}}
                                        <a href="{{ $miniStack->getObjectUrl($bucket->ministack_name, $file['key']) }}"
                                            target="_blank" class="text-[11px] text-accent-bright hover:underline">
                                            Download
                                        </a>
                                        {{-- Hapus --}}
                                        <form action="{{ route('storage.delete-object', $bucket->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus file ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="file_name" value="{{ $file['key'] }}">
                                            <button type="submit" class="text-[11px] text-red-400 hover:text-red-300">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="py-16 text-center text-ink-dim">
                    <div class="text-[40px] mb-3">📂</div>
                    <div class="text-[13px]">Bucket ini masih kosong. Upload file pertamamu!</div>
                </div>
            @endif
        </div>

    </div>
@endsection
