@extends('layouts.app')
@section('title', 'Access Keys')

@section('content')
    <div class="bg-card border border-rim rounded-2xl overflow-hidden">

        <div class="flex items-center justify-between px-6 py-4 border-b border-rim">
            <div>
                <h2 class="text-[16px] font-bold text-ink-primary">🔑 Access Keys</h2>
                <p class="text-[12px] text-ink-muted mt-0.5">
                    {{ $credentials->count() }} / {{ $keyLimit }} key digunakan
                </p>
            </div>

            @if ($credentials->count() < $keyLimit)
                <form action="{{ route('credentials.store') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary text-sm px-4 py-2">
                        + Buat Access Key
                    </button>
                </form>
            @else
                <span class="text-[12px] text-ink-muted">Kuota key penuh</span>
            @endif
        </div>

        @if (session('success'))
            <div
                class="mx-6 mt-4 px-4 py-3 bg-accent-green/10 border border-accent-green/20 rounded-lg text-[13px] text-accent-green">
                {{ session('success') }}
            </div>
        @endif

        <div class="px-6 py-5">
            @if ($credentials->count() > 0)
                <div class="space-y-3">
                    @foreach ($credentials as $cred)
                        <div class="bg-field border border-rim rounded-xl px-5 py-4">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">

                                    <div class="flex items-center gap-2.5 mb-2">
                                        <span class="text-[11px] text-ink-muted w-24 flex-shrink-0">Access Key</span>
                                        <span
                                            class="font-mono text-[12px] text-accent-bright bg-accent/[0.06] border border-accent/[0.15] rounded-lg px-3 py-1.5 flex-1 truncate">
                                            {{ $cred->access_key }}
                                        </span>
                                        <button onclick="copyText('ak-{{ $cred->id }}')"
                                            class="bg-accent/10 border border-accent/20 rounded-lg text-[13px] px-2.5 py-1.5 flex-shrink-0 hover:bg-accent/20 transition-colors">
                                            📋
                                        </button>
                                    </div>

                                    <div class="flex items-center gap-2.5">
                                        <span class="text-[11px] text-ink-muted w-24 flex-shrink-0">Secret Key</span>
                                        <span id="sk-{{ $cred->id }}"
                                            class="font-mono text-[12px] text-accent-bright bg-accent/[0.06] border border-accent/[0.15] rounded-lg px-3 py-1.5 flex-1 truncate">
                                            ••••••••••••••••••••
                                        </span>
                                        <button
                                            onclick="toggleSecret({{ $cred->id }}, '{{ route('credentials.reveal', $cred->id) }}')"
                                            class="bg-accent/10 border border-accent/20 rounded-lg text-[13px] px-2.5 py-1.5 flex-shrink-0 hover:bg-accent/20 transition-colors">
                                            👁
                                        </button>
                                    </div>

                                </div>

                                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                                    <span class="text-[10px] text-ink-muted">
                                        {{ $cred->created_at->format('d M Y') }}
                                    </span>
                                    <form action="{{ route('credentials.destroy', $cred->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menonaktifkan key ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-[11px] text-red-400 hover:text-red-300 transition-colors">
                                            Revoke
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-16 text-center text-ink-dim">
                    <div class="text-[40px] mb-3">🔑</div>
                    <div class="text-[13px] mb-4">Belum ada access key. Buat sekarang untuk mulai menggunakan API.</div>
                    <form action="{{ route('credentials.store') }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" class="btn btn-primary">+ Buat Access Key Pertama</button>
                    </form>
                </div>
            @endif
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        const revealed = {};

        function copyText(id) {
            const el = document.getElementById(id);
            if (!el) return;
            navigator.clipboard.writeText(el.textContent.trim()).then(() => {
                const btn = el.nextElementSibling;
                const orig = btn.textContent;
                btn.textContent = '✓';
                setTimeout(() => btn.textContent = orig, 1500);
            });
        }

        function toggleSecret(id, url) {
            const el = document.getElementById('sk-' + id);
            if (!el) return;

            if (revealed[id]) {
                el.textContent = '••••••••••••••••••••';
                el.style.color = '';
                revealed[id] = false;
                return;
            }

            fetch(url, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.secret_key) {
                        el.textContent = data.secret_key;
                        el.style.color = 'var(--color-accent-green, #00e5a0)';
                        revealed[id] = true;
                    }
                });
        }
    </script>
@endpush
