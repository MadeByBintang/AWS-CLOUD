@extends('layouts.app')
@section('title', 'Checkout — ' . ($selectedPlan['name'] ?? 'Paket'))

@section('content')

    {{-- ── Breadcrumb ──────────────────────────────────────────── --}}
    <div class="flex items-center gap-2 text-[12px] text-ink-dim font-space mb-6">
        <a href="{{ route('subscriptions.index') }}" class="hover:text-accent-cyan no-underline transition-colors">Langganan</a>
        <span>/</span>
        <span class="text-ink-muted">Checkout</span>
        <span>/</span>
        <span class="text-ink-primary">{{ $selectedPlan['name'] }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 max-w-4xl mx-auto">

        {{-- ── Order Summary (kiri) ────────────────────────────── --}}
        <div class="lg:col-span-2">
            <div class="bg-card border border-rim rounded-2xl overflow-hidden sticky top-24">

                {{-- Plan Header --}}
                <div class="px-6 py-5 border-b border-rim/60"
                    style="background: linear-gradient(135deg, #0d1a31 0%, #091220 100%)">
                    <div class="text-[11px] font-space text-ink-dim tracking-[1.5px] uppercase mb-2">Paket Dipilih</div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-[20px]
                            @if($plan === 'pro') bg-accent/20 border border-accent/30
                            @elseif($plan === 'starter') bg-accent-cyan/10 border border-accent-cyan/20
                            @else bg-accent-purple/10 border border-accent-purple/20 @endif">
                            @if($plan === 'starter') 🚀 @elseif($plan === 'pro') 🏢 @else ⭐ @endif
                        </div>
                        <div>
                            <div class="text-[20px] font-extrabold text-ink-primary">{{ $selectedPlan['name'] }}</div>
                            <div class="text-[12px] text-ink-muted">Storage Plan</div>
                        </div>
                    </div>
                    <div class="text-[28px] font-extrabold text-ink-primary">
                        Rp <span class="text-accent-bright">{{ number_format($selectedPlan['price'], 0, ',', '.') }}</span>
                        <span class="text-[14px] font-normal text-ink-dim">/bulan</span>
                    </div>
                </div>

                {{-- Plan Features --}}
                <div class="px-6 py-5">
                    <div class="text-[11px] font-space text-ink-dim tracking-[1px] uppercase mb-3">Yang Anda Dapatkan</div>
                    <ul class="space-y-2.5">
                        <li class="flex items-center gap-2.5 text-[13px] text-ink-muted">
                            <span class="text-accent-green text-[14px]">✓</span>
                            <span>{{ $selectedPlan['quota_gb'] }} GB Storage</span>
                        </li>
                        <li class="flex items-center gap-2.5 text-[13px] text-ink-muted">
                            <span class="text-accent-green text-[14px]">✓</span>
                            <span>
                                @if(($selectedPlan['bucket_limit'] ?? 0) >= 9999) Unlimited Bucket
                                @else {{ $selectedPlan['bucket_limit'] }} Bucket @endif
                            </span>
                        </li>
                        <li class="flex items-center gap-2.5 text-[13px] text-ink-muted">
                            <span class="text-accent-green text-[14px]">✓</span>
                            <span>Aktif selama 30 hari</span>
                        </li>
                        <li class="flex items-center gap-2.5 text-[13px] text-ink-muted">
                            <span class="text-accent-green text-[14px]">✓</span>
                            <span>Enkripsi AES-256</span>
                        </li>
                    </ul>

                    <div class="mt-5 pt-4 border-t border-rim/50">
                        <div class="flex justify-between text-[13px] text-ink-muted mb-1">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($selectedPlan['price'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-[13px] text-ink-muted mb-3">
                            <span>PPN (0%)</span>
                            <span>Rp 0</span>
                        </div>
                        <div class="flex justify-between text-[15px] font-bold text-ink-primary">
                            <span>Total</span>
                            <span class="text-accent-bright">Rp {{ number_format($selectedPlan['price'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Payment Form (kanan) ────────────────────────────── --}}
        <div class="lg:col-span-3">
            <div class="bg-card border border-rim rounded-2xl overflow-hidden">
                <div class="px-7 py-5 border-b border-rim/60">
                    <h2 class="text-[20px] font-bold text-ink-primary m-0">Konfirmasi Upgrade</h2>
                    <p class="text-[13px] text-ink-muted mt-1">Pilih metode pembayaran dan selesaikan upgrade Anda.</p>
                </div>

                <div class="px-7 py-6">

                    {{-- Metode Pembayaran (Simulasi) --}}
                    <div class="mb-6">
                        <div class="text-[12px] font-space text-ink-dim tracking-[1px] uppercase mb-3">Metode Pembayaran</div>
                        <div class="grid grid-cols-2 gap-3" id="paymentMethods">
                            @foreach([
                                ['id' => 'transfer', 'label' => 'Transfer Bank', 'icon' => '🏦'],
                                ['id' => 'qris', 'label' => 'QRIS', 'icon' => '📱'],
                                ['id' => 'ewallet', 'label' => 'E-Wallet', 'icon' => '💳'],
                                ['id' => 'virtual', 'label' => 'Virtual Account', 'icon' => '🔢'],
                            ] as $method)
                                <div class="payment-option border border-rim rounded-xl px-4 py-3 flex items-center gap-3 cursor-pointer transition-all hover:border-accent/60 hover:bg-accent/[0.05]"
                                    onclick="selectPayment('{{ $method['id'] }}', this)">
                                    <span class="text-[18px]">{{ $method['icon'] }}</span>
                                    <span class="text-[13px] text-ink-muted font-semibold">{{ $method['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" id="selectedPaymentMethod" value="">
                        <div id="paymentError" class="text-[12px] text-red-400 mt-2 hidden">Pilih metode pembayaran terlebih dahulu.</div>
                    </div>

                    {{-- Kode Promo --}}
                    <div class="mb-6">
                        <div class="text-[12px] font-space text-ink-dim tracking-[1px] uppercase mb-2">Kode Promo (Opsional)</div>
                        <div class="flex gap-2">
                            <input type="text" id="promoCode" placeholder="Masukkan kode promo..."
                                class="field-input flex-1 text-[13px]">
                            <button onclick="applyPromo()" class="px-4 py-2 bg-base border border-rim rounded-lg text-[13px] text-ink-muted hover:text-ink-primary hover:border-rim-light transition-colors cursor-pointer">
                                Pakai
                            </button>
                        </div>
                        <div id="promoMsg" class="text-[12px] mt-1.5 hidden"></div>
                    </div>

                    {{-- Persetujuan --}}
                    <div class="flex items-start gap-2.5 mb-6 bg-base/50 border border-rim/40 rounded-xl px-4 py-3">
                        <input type="checkbox" id="agreeTerms" class="mt-0.5 w-4 h-4 accent-accent flex-shrink-0 cursor-pointer">
                        <label for="agreeTerms" class="text-[12px] text-ink-muted leading-[1.6] cursor-pointer">
                            Saya menyetujui bahwa langganan ini akan aktif selama 30 hari dan biaya sebesar
                            <strong class="text-ink-primary">Rp {{ number_format($selectedPlan['price'], 0, ',', '.') }}</strong>
                            telah dikonfirmasi. Paket ini adalah simulasi dan tidak ada transaksi nyata yang terjadi.
                        </label>
                    </div>

                    {{-- Submit Form --}}
                    <form method="POST" action="{{ route('subscriptions.store') }}" id="checkoutForm">
                        @csrf
                        <input type="hidden" name="plan" value="{{ $plan }}">
                        <button type="button" onclick="submitCheckout()"
                            class="w-full relative overflow-hidden border-0 rounded-xl px-5 py-4
                                   font-syne text-[15px] font-bold text-white cursor-pointer tracking-[0.3px]
                                   transition-all hover:-translate-y-px active:translate-y-0
                                   @if($plan === 'pro') bg-accent-purple hover:bg-accent-purple/90 shadow-[0_4px_20px_rgba(139,92,246,0.4)]
                                   @else bg-grad-accent shadow-accent hover:shadow-accent-lg @endif">
                            <span id="btnText">
                                🚀 Konfirmasi & Aktifkan Paket {{ $selectedPlan['name'] }}
                            </span>
                        </button>
                    </form>

                    <p class="text-center text-[11px] text-ink-dim mt-3">
                        🔒 Transaksi diamankan dengan enkripsi end-to-end · Tidak ada kartu kredit diperlukan (simulasi)
                    </p>
                </div>
            </div>

            {{-- Back link --}}
            <div class="mt-4 text-center">
                <a href="{{ route('subscriptions.index') }}" class="text-[13px] text-ink-dim hover:text-ink-muted no-underline transition-colors">
                    ← Kembali ke Halaman Paket
                </a>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function selectPayment(id, el) {
        document.querySelectorAll('.payment-option').forEach(opt => {
            opt.classList.remove('border-accent', 'bg-accent/[0.08]', 'text-accent-bright');
            opt.classList.add('border-rim');
        });
        el.classList.add('border-accent', 'bg-accent/[0.08]');
        el.classList.remove('border-rim');
        document.getElementById('selectedPaymentMethod').value = id;
        document.getElementById('paymentError').classList.add('hidden');
    }

    function applyPromo() {
        const code = document.getElementById('promoCode').value.trim().toUpperCase();
        const msg = document.getElementById('promoMsg');
        msg.classList.remove('hidden');
        if (code === 'MINISTACK20') {
            msg.textContent = '✓ Kode promo berhasil! Diskon 20% diterapkan (simulasi).';
            msg.className = 'text-[12px] mt-1.5 text-accent-green';
        } else if (code === '') {
            msg.textContent = 'Masukkan kode promo terlebih dahulu.';
            msg.className = 'text-[12px] mt-1.5 text-red-400';
        } else {
            msg.textContent = '✕ Kode promo tidak valid atau sudah kadaluarsa.';
            msg.className = 'text-[12px] mt-1.5 text-red-400';
        }
    }

    function submitCheckout() {
        const method = document.getElementById('selectedPaymentMethod').value;
        const agreed = document.getElementById('agreeTerms').checked;

        if (!method) {
            document.getElementById('paymentError').classList.remove('hidden');
            return;
        }
        if (!agreed) {
            alert('Mohon centang persetujuan terlebih dahulu.');
            return;
        }

        // Simulate loading
        const btn = document.getElementById('btnText');
        btn.textContent = '⏳ Memproses pembayaran...';

        // Submit form after short delay for UX
        setTimeout(() => {
            document.getElementById('checkoutForm').submit();
        }, 1200);
    }
</script>
@endpush
