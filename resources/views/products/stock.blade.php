@extends('layouts.app')

@section('title', 'Stock Management')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Stock Management</h1>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Total Stock</th>
                        <th>Selling Price</th>
                        <th>Batches</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr class="{{ $product->is_low_stock ? 'table-warning' : '' }}">
                        <td>
                            {{ $product->name }}
                            @if($product->is_low_stock)
                            <span class="badge bg-warning text-dark">Low Stock</span>
                            @endif
                        </td>
                        <td>{{ $product->category->name }}</td>
                        <td><strong>{{ $product->current_stock }}</strong></td>
                        <td>
                            <input type="number" class="form-control form-control-sm d-inline-block" 
                                   style="width: 120px;" value="{{ $product->selling_price }}" 
                                   step="0.01" id="price_{{ $product->id }}">
                        </td>
                        <td>
                            @if($product->batches->count() > 0)
                            <button class="btn btn-sm btn-info" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#batches_{{ $product->id }}">
                                View {{ $product->batches->count() }} Batches
                            </button>
                            @else
                            <small class="text-muted">No batches</small>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="updatePrice({{ $product->id }})">
                                Update Price
                            </button>
                        </td>
                    </tr>
                    @if($product->batches->count() > 0)
                    <tr>
                        <td colspan="6" class="p-0">
                            <div class="collapse" id="batches_{{ $product->id }}">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Batch</th>
                                            <th>Quantity</th>
                                            <th>Buying Price</th>
                                            <th>Expiry Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($product->batches as $batch)
                                        <tr>
                                            <td>{{ $batch->batch_number ?? 'N/A' }}</td>
                                            <td>{{ $batch->quantity }}</td>
                                            <td>Tsh {{ number_format($batch->buying_price, 0) }}</td>
                                            <td>{{ $batch->expiry_date?->format('d M Y') ?? 'N/A' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updatePrice(productId) {
    const price = document.getElementById(`price_${productId}`).value;
    
    fetch(`/products/${productId}/update-price`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ selling_price: price })
    })
    .then(r => r.json())
    .then(data => {
        alert('Price updated successfully');
    })
    .catch(err => {
        alert('Failed to update price');
    });
}
</script>
@endpush
