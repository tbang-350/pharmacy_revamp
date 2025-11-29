@extends('layouts.app')

@section('title', 'New Sale')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">New Sale</h1>
</div>

<div class="row">
    <!-- Left Panel - Product Search and Add -->
    <div class="col-md-7">
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-search"></i> Search Product</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" class="form-control form-control-lg" id="productSearch" 
                           placeholder="Type product name... (Press F2 to focus)" autofocus>
                </div>
                <div id="searchResults" class="list-group"></div>
                
                <!-- Frequently Bought Items -->
                <div class="mt-4">
                    <h6 class="text-muted mb-3"><i class="bi bi-lightning-charge"></i> Frequently Bought Items</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($frequentProducts as $product)
                        <button class="btn btn-outline-primary" 
                                onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->selling_price }}, {{ $product->current_stock }})">
                            {{ $product->name }}
                            <small class="d-block text-muted">Tsh {{ number_format($product->selling_price) }}</small>
                        </button>
                        @endforeach
                        @if($frequentProducts->isEmpty())
                        <p class="text-muted small">No sales data yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel - Cart -->
    <div class="col-md-5">
        <div class="card position-sticky" style="top: 20px;">
            <div class="card-header bg-success text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-cart"></i> Cart</h5>
                    <button type="button" class="btn btn-sm btn-outline-light" onclick="clearCart()">Clear</button>
                </div>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                <div id="cartItems">
                    <p class="text-center text-muted">Cart is empty</p>
                </div>
            </div>
            <div class="card-footer">
                <div class="mb-2">
                    <label class="form-label">Overall Discount</label>
                    <input type="number" class="form-control" id="overallDiscount" 
                           value="0" min="0" step="0.01" onchange="updateCartTotals()">
                </div>
                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <select class="form-select" id="paymentMethod" required>
                        <option value="cash">Cash</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <strong>Subtotal:</strong>
                    <span id="cartSubtotal">Tsh 0</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <strong>Discount:</strong>
                    <span id="cartDiscount">Tsh 0</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <h5>Total:</h5>
                    <h5 id="cartTotal">Tsh 0</h5>
                </div>
                <button type="button" class="btn btn-success btn-lg w-100" onclick="checkout()">
                    <i class="bi bi-check-circle"></i> Complete Sale
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let cart = {};
let debounceTimer;

// Product search with autocomplete
document.getElementById('productSearch').addEventListener('input', function(e) {
    clearTimeout(debounceTimer);
    const term = e.target.value;
    
    if(term.length < 2) {
        document.getElementById('searchResults').innerHTML = '';
        return;
    }
    
    debounceTimer = setTimeout(() => {
        fetch(`/sales/search-product?term=${encodeURIComponent(term)}`)
            .then(r => r.json())
            .then(products => {
                displaySearchResults(products);
            });
    }, 300);
});

function displaySearchResults(products) {
    const resultsDiv = document.getElementById('searchResults');
    
    if(products.length === 0) {
        resultsDiv.innerHTML = '<div class="list-group-item">No products found</div>';
        return;
    }
    
    resultsDiv.innerHTML = products.map(product => `
        <a href="#" class="list-group-item list-group-item-action" onclick="addToCart(${product.id}, '${product.name.replace(/'/g, "\\'")}', ${product.price}, ${product.stock}); return false;">
            <div class="d-flex justify-content-between">
                <div>
                    <strong>${product.name}</strong>
                    <br><small class="text-muted">${product.category}</small>
                </div>
                <div class="text-end">
                    <strong>Tsh ${numberFormat(product.price)}</strong>
                    <br><small class="${product.stock > 0 ? 'text-success' : 'text-danger'}">Stock: ${product.stock}</small>
                </div>
            </div>
        </a>
    `).join('');
}

function addToCart(productId, productName, price, stock) {
    if(stock <= 0) {
        Swal.fire('Out of Stock', 'This product is currently unavailable.', 'error');
        return;
    }
    
    const cartId = `product_${productId}`;
    
    if(cart[cartId]) {
        cart[cartId].quantity++;
    } else {
        cart[cartId] = {
            product_id: productId,
            name: productName,
            quantity: 1,
            price: price,
            discount: 0,
            stock: stock
        };
    }
    
    // Clear search
    document.getElementById('productSearch').value = '';
    document.getElementById('searchResults').innerHTML = '';
    document.getElementById('productSearch').focus();
    
    updateCartDisplay();
}

function updateCartDisplay() {
    const cartItemsDiv = document.getElementById('cartItems');
    
    if(Object.keys(cart).length === 0) {
        cartItemsDiv.innerHTML = '<p class="text-center text-muted">Cart is empty</p>';
        updateCartTotals();
        return;
    }
    
    cartItemsDiv.innerHTML = Object.entries(cart).map(([cartId, item]) => `
        <div class="mb-3 pb-3 border-bottom">
            <div class="d-flex justify-content-between mb-2">
                <strong>${item.name}</strong>
                <button class="btn btn-sm btn-danger" onclick="removeFromCart('${cartId}')">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="row g-2">
                <div class="col-6">
                    <label class="form-label-sm">Qty</label>
                    <input type="number" class="form-control form-control-sm" 
                           value="${item.quantity}" min="1" max="${item.stock}"
                           onchange="updateQuantity('${cartId}', this.value)">
                </div>
                <div class="col-6">
                    <label class="form-label-sm">Discount</label>
                    <input type="number" class="form-control form-control-sm" 
                           value="${item.discount}" min="0" step="0.01"
                           onchange="updateDiscount('${cartId}', this.value)">
                </div>
            </div>
            <div class="text-end mt-2">
                <small>Tsh ${numberFormat(item.price)} × ${item.quantity} = </small>
                <strong>Tsh ${numberFormat(item.price * item.quantity - item.discount)}</strong>
            </div>
        </div>
    `).join('');
    
    updateCartTotals();
}

function updateQuantity(cartId, quantity) {
    quantity = parseInt(quantity);
    if(quantity > cart[cartId].stock) {
        Swal.fire('Stock Limit Reached', `Only ${cart[cartId].stock} units available`, 'warning');
        return;
    }
    cart[cartId].quantity = quantity;
    updateCartDisplay();
}

function updateDiscount(cartId, discount) {
    cart[cartId].discount = parseFloat(discount);
    updateCartDisplay();
}

function removeFromCart(cartId) {
   delete cart[cartId];
    updateCartDisplay();
}

function clearCart() {
    Swal.fire({
        title: 'Clear Cart?',
        text: 'Are you sure you want to remove all items?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Clear it'
    }).then((result) => {
        if (result.isConfirmed) {
            cart = {};
            updateCartDisplay();
        }
    });
}

function updateCartTotals() {
    const subtotal = Object.values(cart).reduce((sum, item) => 
        sum + (item.price * item.quantity - item.discount), 0);
    
    const overallDiscount = parseFloat(document.getElementById('overallDiscount').value) || 0;
    const total = subtotal - overallDiscount;
    
    document.getElementById('cartSubtotal').textContent = 'Tsh ' + numberFormat(subtotal);
    document.getElementById('cartDiscount').textContent = 'Tsh ' + numberFormat(overallDiscount);
    document.getElementById('cartTotal').textContent = 'Tsh ' + numberFormat(total);
}

function checkout() {
    if(Object.keys(cart).length === 0) {
        Swal.fire('Cart is Empty', 'Please add items to the cart first.', 'warning');
        return;
    }
    
    const paymentMethod = document.getElementById('paymentMethod').value;
    const paymentLabel = document.getElementById('paymentMethod').options[document.getElementById('paymentMethod').selectedIndex].text;
    const overallDiscount = parseFloat(document.getElementById('overallDiscount').value) || 0;
    
    // Calculate totals for display
    const subtotal = Object.values(cart).reduce((sum, item) => 
        sum + (item.price * item.quantity - item.discount), 0);
    const total = subtotal - overallDiscount;
    const itemCount = Object.values(cart).reduce((sum, item) => sum + item.quantity, 0);

    Swal.fire({
        title: 'Confirm Sale?',
        html: `
            <div class="text-start">
                <p><strong>Items:</strong> ${itemCount}</p>
                <p><strong>Total:</strong> Tsh ${numberFormat(total)}</p>
                <p><strong>Payment:</strong> ${paymentLabel}</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Complete Sale',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            submitSale(paymentMethod, overallDiscount);
        }
    });
}

function submitSale(paymentMethod, overallDiscount) {
    // Submit checkout form with cart data
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("sales.checkout") }}';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
    form.appendChild(csrfInput);
    
    const paymentInput = document.createElement('input');
    paymentInput.type = 'hidden';
    paymentInput.name = 'payment_method';
    paymentInput.value = paymentMethod;
    form.appendChild(paymentInput);
    
    const discountInput = document.createElement('input');
    discountInput.type = 'hidden';
    discountInput.name = 'overall_discount';
    discountInput.value = overallDiscount;
    form.appendChild(discountInput);

    // Add cart items to form
    Object.entries(cart).forEach(([cartId, item], index) => {
        for (const [key, value] of Object.entries(item)) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `cart[${cartId}][${key}]`;
            input.value = value;
            form.appendChild(input);
        }
    });
    
    document.body.appendChild(form);
    form.submit();
}

function numberFormat(number) {
    return new Intl.NumberFormat().format(number);
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if(e.key === 'F2') {
        e.preventDefault();
        document.getElementById('productSearch').focus();
    }
});

// Initialize
updateCartDisplay();
</script>
@endpush
