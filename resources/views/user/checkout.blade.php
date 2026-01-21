{{-- resources/views/user/checkout.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h3 class="mb-4">
                <i class="bi bi-credit-card"></i> Checkout Pesanan
            </h3>

            {{-- Order Items --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Detail Pesanan</h5>
                </div>
                <div class="card-body p-0">
                    <div id="checkout-items" class="list-group list-group-flush">
                        {{-- Rendered by JavaScript --}}
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>Total Pembayaran:</strong>
                        <strong class="fs-4 text-primary">Rp <span id="checkout-total">0</span></strong>
                    </div>
                </div>
            </div>

            {{-- Customer Information --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Informasi Pemesan</h5>
                </div>
                <div class="card-body">
                    <form id="checkout-form">
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="customer_phone" class="form-label">Nomor WhatsApp <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="customer_phone" name="customer_phone" placeholder="08xxxxxxxxxx" required>
                            <small class="text-muted">Format: 08xxxxxxxxxx</small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="submit-order-btn">
                                <i class="bi bi-send"></i> Pesan Sekarang
                            </button>
                            <a href="{{ route('user.menu.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali ke Menu
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Include Cart JS --}}
<script src="{{ asset('js/cart.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if cart has items
    if (cart.cart.items.length === 0) {
        alert('Keranjang kosong! Silakan pilih menu terlebih dahulu.');
        window.location.href = "{{ route('user.menu.index') }}";
        return;
    }

    // Render checkout items
    renderCheckoutItems();

    // Handle form submission
    document.getElementById('checkout-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submit-order-btn');
        const originalText = submitBtn.innerHTML;
        
        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        // Prepare order data
        const orderData = {
            customer_name: document.getElementById('customer_name').value,
            customer_phone: document.getElementById('customer_phone').value,
            items: cart.cart.items.map(item => ({
                menu_id: item.menu_id,
                quantity: item.quantity
            }))
        };

        try {
            const response = await fetch('/api/orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(orderData)
            });

            const result = await response.json();

            if (response.ok && result.success) {
                // Clear cart
                cart.clearCart();
                
                // Redirect to success page
                window.location.href = "{{ route('user.order.success') }}?code=" + result.data.kode_pesanan;
            } else {
                // Handle validation errors
                if (result.errors) {
                    Object.keys(result.errors).forEach(field => {
                        const input = document.getElementById(field);
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = input.nextElementSibling;
                            if (feedback && feedback.classList.contains('invalid-feedback')) {
                                feedback.textContent = result.errors[field][0];
                            }
                        }
                    });
                }
                
                alert('Error: ' + (result.message || 'Terjadi kesalahan'));
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
});

function renderCheckoutItems() {
    const container = document.getElementById('checkout-items');
    const totalElement = document.getElementById('checkout-total');
    
    let html = '';
    cart.cart.items.forEach((item, index) => {
        html += `
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">${index + 1}. ${item.nama_menu}</h6>
                        <small class="text-muted">Rp ${cart.formatRupiah(item.harga)} × ${item.quantity}</small>
                    </div>
                    <strong class="text-primary">Rp ${cart.formatRupiah(item.subtotal)}</strong>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    totalElement.textContent = cart.formatRupiah(cart.cart.total);
}
</script>

@endsection
