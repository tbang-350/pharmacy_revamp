@extends('layouts.app')

@section('title', 'Stock Management')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Stock Management</h1>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="stockTable">
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
                            <button class="btn btn-sm btn-info text-white" type="button" data-bs-toggle="modal" 
                                    data-bs-target="#batchesModal{{ $product->id }}">
                                View {{ $product->batches->count() }} Batches
                            </button>
                            @else
                            <small class="text-muted">No batches</small>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary update-price-btn" data-id="{{ $product->id }}">
                                Update Price
                            </button>
                        </td>
                    </tr>

                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Batches Modals -->
        @foreach($products as $product)
            @if($product->batches->count() > 0)
            <div class="modal fade" id="batchesModal{{ $product->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $product->name }} - Batches</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Batch</th>
                                            <th>Qty</th>
                                            <th>Buying Price</th>
                                            <th>Expiry</th>
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
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach

    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#stockTable').DataTable({
        pageLength: 25,
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: [4, 5] } // Disable sorting on Batches and Action columns
        ],
        language: {
            search: "Search stock:",
            lengthMenu: "Show _MENU_ items per page",
            info: "Showing _START_ to _END_ of _TOTAL_ items"
        }
    });

    // Handle price update using event delegation
    $('#stockTable tbody').on('click', '.update-price-btn', function() {
        var productId = $(this).data('id');
        updatePrice(productId);
    });
});

function updatePrice(productId) {
    const priceInput = document.getElementById(`price_${productId}`);
    const price = priceInput.value;
    
    Swal.fire({
        title: 'Update Price?',
        text: `Are you sure you want to update the price to ${price}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch(`/products/${productId}/update-price`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ selling_price: price })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(response.statusText)
                }
                return response.json()
            })
            .catch(error => {
                Swal.showValidationMessage(
                    `Request failed: ${error}`
                )
            })
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Updated!',
                text: 'Product price has been updated.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}
</script>
@endpush
