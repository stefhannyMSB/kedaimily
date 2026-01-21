{{-- resources/views/user/menu/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Daftar Menu</h3>
        
        {{-- Cart Button --}}
        <button class="btn btn-primary position-relative" data-bs-toggle="modal" data-bs-target="#cartModal">
            <i class="bi bi-cart3"></i> Keranjang
            <span id="cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">
                0
            </span>
        </button>
    </div>

    {{-- Menu Cards --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            @if(collect($menus)->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    Belum ada data menu.
                </div>
            @else
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @foreach($menus as $item)
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $item->nama_menu }}</h5>
                                    <p class="card-text text-primary fw-bold fs-5">
                                        Rp {{ number_format($item->harga, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="card-footer bg-white border-0">
                                    <button 
                                        class="btn btn-success btn-sm w-100" 
                                        onclick="cart.addItem({{ $item->id_menu }}, '{{ addslashes($item->nama_menu) }}', {{ $item->harga }})">
                                        <i class="bi bi-plus-circle"></i> Tambah ke Keranjang
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Pagination --}}
        @if(method_exists($menus, 'links'))
            <div class="card-footer bg-white">
                {{ $menus->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Cart Modal --}}
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cartModalLabel">
                    <i class="bi bi-cart3"></i> Keranjang Belanja
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="cart-modal-body">
                {{-- Rendered by JavaScript --}}
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <strong>Total:</strong>
                        <strong class="fs-5 text-primary">Rp <span id="cart-total">0</span></strong>
                    </div>
                    <a href="{{ route('user.checkout') }}" class="btn btn-primary w-100">
                        <i class="bi bi-credit-card"></i> Checkout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Include Cart JS --}}
<script src="{{ asset('js/cart.js') }}"></script>

<script>
    // Show cart modal when opened
    document.getElementById('cartModal').addEventListener('show.bs.modal', function () {
        cart.renderCartModal();
    });
</script>

@endsection
