// Shopping Cart Management with LocalStorage
class ShoppingCart {
    constructor() {
        this.storageKey = 'kedaimily_cart';
        this.cart = this.loadCart();
    }

    loadCart() {
        const stored = localStorage.getItem(this.storageKey);
        return stored ? JSON.parse(stored) : { items: [], total: 0 };
    }

    saveCart() {
        localStorage.setItem(this.storageKey, JSON.stringify(this.cart));
        this.updateUI();
    }

    addItem(menuId, namaMenu, harga) {
        const existingItem = this.cart.items.find(item => item.menu_id == menuId);
        
        if (existingItem) {
            existingItem.quantity++;
            existingItem.subtotal = existingItem.quantity * existingItem.harga;
        } else {
            this.cart.items.push({
                menu_id: menuId,
                nama_menu: namaMenu,
                harga: parseFloat(harga),
                quantity: 1,
                subtotal: parseFloat(harga)
            });
        }
        
        this.calculateTotal();
        this.saveCart();
        this.showNotification('Item ditambahkan ke keranjang');
    }

    removeItem(menuId) {
        this.cart.items = this.cart.items.filter(item => item.menu_id != menuId);
        this.calculateTotal();
        this.saveCart();
    }

    updateQuantity(menuId, quantity) {
        const item = this.cart.items.find(item => item.menu_id == menuId);
        if (item) {
            item.quantity = parseInt(quantity);
            if (item.quantity <= 0) {
                this.removeItem(menuId);
            } else {
                item.subtotal = item.quantity * item.harga;
                this.calculateTotal();
                this.saveCart();
            }
        }
    }

    calculateTotal() {
        this.cart.total = this.cart.items.reduce((sum, item) => sum + item.subtotal, 0);
    }

    getItemCount() {
        return this.cart.items.reduce((sum, item) => sum + item.quantity, 0);
    }

    clearCart() {
        this.cart = { items: [], total: 0 };
        this.saveCart();
    }

    updateUI() {
        // Update cart badge
        const badge = document.getElementById('cart-badge');
        const count = this.getItemCount();
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
        }

        // Update cart modal if open
        this.renderCartModal();
    }

    renderCartModal() {
        const modalBody = document.getElementById('cart-modal-body');
        const cartTotal = document.getElementById('cart-total');
        
        if (!modalBody) return;

        if (this.cart.items.length === 0) {
            modalBody.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="bi bi-cart-x fs-1"></i>
                    <p class="mt-2">Keranjang kosong</p>
                </div>
            `;
            if (cartTotal) cartTotal.textContent = '0';
            return;
        }

        let html = '<div class="list-group list-group-flush">';
        this.cart.items.forEach(item => {
            html += `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${item.nama_menu}</h6>
                            <small class="text-muted">Rp ${this.formatRupiah(item.harga)}</small>
                        </div>
                        <button class="btn btn-sm btn-outline-danger" onclick="cart.removeItem(${item.menu_id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary" onclick="cart.updateQuantity(${item.menu_id}, ${item.quantity - 1})">-</button>
                            <input type="number" class="form-control form-control-sm text-center" style="width: 60px;" value="${item.quantity}" min="1" onchange="cart.updateQuantity(${item.menu_id}, this.value)">
                            <button class="btn btn-outline-secondary" onclick="cart.updateQuantity(${item.menu_id}, ${item.quantity + 1})">+</button>
                        </div>
                        <strong>Rp ${this.formatRupiah(item.subtotal)}</strong>
                    </div>
                </div>
            `;
        });
        html += '</div>';

        modalBody.innerHTML = html;
        if (cartTotal) {
            cartTotal.textContent = this.formatRupiah(this.cart.total);
        }
    }

    formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    showNotification(message) {
        // Simple toast notification
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 p-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="toast show" role="alert">
                <div class="toast-body bg-success text-white rounded">
                    <i class="bi bi-check-circle me-2"></i>${message}
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    }
}

// Initialize cart
const cart = new ShoppingCart();

// Update UI on page load
document.addEventListener('DOMContentLoaded', function() {
    cart.updateUI();
});
