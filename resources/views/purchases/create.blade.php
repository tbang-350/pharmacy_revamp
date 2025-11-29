@extends('layouts.app')

@section('title', 'New Purchase')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">New Purchase</h1>
</div>

<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#manual">Manual Entry</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#excel">Excel Import</a>
    </li>
</ul>

<div class="tab-content mt-3">
    <!-- Manual Entry Tab -->
    <div class="tab-pane fade show active" id="manual">
        <form method="POST" action="{{ route('purchases.store') }}">
            @csrf
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Purchase Date</label>
                    <input type="date" class="form-control" name="purchase_date" 
                           value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Supplier (Optional)</label>
                    <select class="form-select" name="supplier_id">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Notes</label>
                    <input type="text" class="form-control" name="notes">
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5>Purchase Items</h5>
                </div>
                <div class="card-body">
                    <div id="purchaseItems">
                        <div class="purchase-item border p-3 mb-3">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label">Product Name</label>
                                    <input type="text" class="form-control product-search" 
                                           name="items[0][name]" required>
                                    <input type="hidden" name="items[0][product_id]" class="product-id">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Category</label>
                                    <select class="form-select" name="items[0][category_id]" required>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Qty</label>
                                    <input type="number" class="form-control" name="items[0][quantity]" 
                                           required min="1" value="1">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Buying Price</label>
                                    <input type="number" class="form-control" name="items[0][buying_price]" 
                                           required step="0.01" min="0">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Selling Price</label>
                                    <input type="number" class="form-control" name="items[0][selling_price]" 
                                           required step="0.01" min="0">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Batch</label>
                                    <input type="text" class="form-control" name="items[0][batch_number]">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Expiry</label>
                                    <input type="date" class="form-control" name="items[0][expiry_date]">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger w-100 remove-item" onclick="removePurchaseItem(this)" style="display: none;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary" onclick="addPurchaseItem()">
                        <i class="bi bi-plus"></i> Add Another Item
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">Save Purchase</button>
        </form>
    </div>

    <!-- Excel Import Tab -->
    <div class="tab-pane fade" id="excel">
        <form method="POST" action="{{ route('purchases.import-excel') }}" enctype="multipart/form-data" onsubmit="showImportLoader()">
            @csrf
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Purchase Date</label>
                    <input type="date" class="form-control" name="purchase_date" 
                           value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Supplier (Optional)</label>
                    <select class="form-select" name="supplier_id">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>Excel Format:</strong> Columns should be: Name, Category, Quantity, Buying Price, Selling Price, Batch Number, Expiry Date
            </div>

            <div class="mb-3">
                <label class="form-label">Excel File</label>
                <div class="input-group">
                    <input type="file" class="form-control" name="excel_file" accept=".xlsx,.xls,.csv" required>
                    <a href="{{ route('purchases.template') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-download"></i> Download Template
                    </a>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">Import Purchase</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let itemCount = 1;

function addPurchaseItem() {
    const itemsDiv = document.getElementById('purchaseItems');
    const firstItem = itemsDiv.querySelector('.purchase-item');
    const newItem = firstItem.cloneNode(true);
    
    // Update input names and clear values
    newItem.querySelectorAll('input, select').forEach(input => {
        const name = input.name.replace(/\[\d+\]/, `[${itemCount}]`);
        input.name = name;
        input.value = '';
    });
    
    // Show remove button
    newItem.querySelector('.remove-item').style.display = 'block';
    
    itemsDiv.appendChild(newItem);
    itemCount++;
}

function removePurchaseItem(button) {
    const item = button.closest('.purchase-item');
    const itemsDiv = document.getElementById('purchaseItems');
    
    if(itemsDiv.querySelectorAll('.purchase-item').length > 1) {
        item.remove();
    }
}

// Product autocomplete
document.addEventListener('input', function(e) {
    if(e.target.classList.contains('product-search')) {
        const term = e.target.value;
        if(term.length < 2) return;
        
        fetch(`/purchases/search-product?term=${encodeURIComponent(term)}`)
            .then(r => r.json())
            .then(products => {
                // For simplicity, auto-fill if exact match
                if(products.length > 0) {
                    const product = products[0];
                    const item = e.target.closest('.purchase-item');
                    item.querySelector('.product-id').value = product.id;
                    item.querySelector('[name*="selling_price"]').value = product.selling_price;
                }
            });
    }
});

function showImportLoader() {
    Swal.fire({
        title: 'Importing Purchases',
        text: 'Please wait while we process your file...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}
</script>
@endpush
