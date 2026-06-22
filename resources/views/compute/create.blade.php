@extends('layouts.app')
@section('title', 'Launch Instance')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('compute.index') }}" class="text-[13px] text-ink-muted hover:text-ink-primary flex items-center gap-1.5 mb-3">
            ← Kembali ke Compute
        </a>
        <h1 class="text-[22px] font-extrabold text-ink-primary">Launch Instance Baru</h1>
        <p class="text-[13px] text-ink-muted mt-1">Konfigurasi VM Anda. Paket aktif: <strong class="text-accent-cyan">{{ $computeSub->displayName() }}</strong>
            (max {{ $computeSub->vcpu_limit }} vCPU, {{ $computeSub->ram_go }} GB RAM)</p>
    </div>

    <form action="{{ route('compute.store') }}" method="POST" id="launchForm">
        @csrf

        {{-- Nama Instance --}}
        <div class="bg-card border border-rim rounded-2xl p-5 mb-4">
            <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mb-3">1. Nama Instance</div>
            <input type="text" name="name" value="{{ old('name') }}"
                class="w-full bg-field border border-rim rounded-xl px-4 py-2.5 text-ink-primary text-[14px] focus:outline-none focus:border-accent transition-colors"
                placeholder="misal: web-server-prod" pattern="[a-zA-Z0-9\-_]+" required>
            <p class="text-[11px] text-ink-muted mt-2">Hanya huruf, angka, strip (-) dan underscore (_).</p>
            @error('name')<p class="text-[12px] text-red-400 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- OS Image --}}
        <div class="bg-card border border-rim rounded-2xl p-5 mb-4">
            <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mb-3">2. Pilih OS Image</div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach ($osImages as $os)
                    <label class="os-option cursor-pointer">
                        <input type="radio" name="os_image" value="{{ $os['id'] }}" class="hidden peer" {{ old('os_image', 'ubuntu-22.04') === $os['id'] ? 'checked' : '' }}>
                        <div class="bg-field border border-rim rounded-xl px-4 py-3 flex items-center gap-3 peer-checked:border-accent peer-checked:bg-accent/5 transition-all">
                            <span class="text-[22px]">{{ $os['icon'] }}</span>
                            <div>
                                <div class="text-[13px] font-bold text-ink-primary">{{ $os['name'] }}</div>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('os_image')<p class="text-[12px] text-red-400 mt-2">{{ $message }}</p>@enderror
        </div>

        {{-- Instance Type --}}
        <div class="bg-card border border-rim rounded-2xl p-5 mb-4">
            <div class="font-space text-[10px] text-ink-muted tracking-widest uppercase mb-3">3. Pilih Tipe Instance</div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach ($instanceTypes as $type)
                    @php $locked = ($type['vcpu'] > $computeSub->vcpu_limit || $type['ram'] > $computeSub->ram_go); @endphp
                    <label class="{{ $locked ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer' }}">
                        <input type="radio" name="instance_type" value="{{ $type['id'] }}" class="hidden peer"
                            {{ old('instance_type', 'nano') === $type['id'] ? 'checked' : '' }}
                            {{ $locked ? 'disabled' : '' }}>
                        <div class="bg-field border border-rim rounded-xl px-4 py-3 peer-checked:border-accent peer-checked:bg-accent/5 transition-all">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[14px] font-bold text-ink-primary">{{ $type['name'] }}</span>
                                @if ($locked)
                                    <span class="font-space text-[9px] text-accent-orange">🔒 Upgrade</span>
                                @endif
                            </div>
                            <div class="font-space text-[11px] text-accent-bright">{{ $type['vcpu'] }} vCPU · {{ $type['ram'] }} GB RAM</div>
                            <div class="text-[11px] text-ink-dim mt-0.5">{{ $type['desc'] }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('instance_type')<p class="text-[12px] text-red-400 mt-2">{{ $message }}</p>@enderror
        </div>

        {{-- Tombol --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('compute.index') }}" class="btn btn-outline px-5">Batal</a>
            <button type="submit" class="btn btn-primary px-8">
                ▶ Launch Instance
            </button>
        </div>
    </form>
</div>
@endsection
