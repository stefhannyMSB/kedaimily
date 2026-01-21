{{-- resources/views/user/order-success.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center py-5">
                    {{-- Success Icon --}}
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    </div>

                    {{-- Success Message --}}
                    <h3 class="mb-3">Pesanan Berhasil!</h3>
                    <p class="text-muted mb-4">
                        Terima kasih! Pesanan Anda telah kami terima dan notifikasi WhatsApp telah dikirim ke penjual.
                    </p>

                    {{-- Order Code --}}
                    @if(request('code'))
                    <div class="alert alert-info mb-4">
                        <div class="mb-2"><strong>Kode Pesanan:</strong></div>
                        <h4 class="mb-0 text-primary">
                            <i class="bi bi-receipt"></i> {{ request('code') }}
                        </h4>
                    </div>
                    @endif

                    {{-- Info Box --}}
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="mb-3">
                                <i class="bi bi-info-circle"></i> Informasi Penting
                            </h6>
                            <ul class="list-unstyled text-start mb-0">
                                <li class="mb-2">
                                    <i class="bi bi-check text-success"></i> Pesanan Anda sedang diproses
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check text-success"></i> Penjual akan segera menghubungi Anda
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check text-success"></i> Simpan kode pesanan untuk referensi
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-grid gap-2">
                        <a href="{{ route('user.menu.index') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-cart-plus"></i> Pesan Lagi
                        </a>
                        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-house"></i> Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Optional: Auto-redirect setelah beberapa detik
    // setTimeout(() => {
    //     window.location.href = "{{ route('user.dashboard') }}";
    // }, 10000); // 10 detik
</script>

@endsection
