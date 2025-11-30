@extends('layouts.app')

@section('title', 'Purchase History')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Purchase History</h1>
    <a href="{{ route('purchases.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Purchase
    </a>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="date" class="form-control" name="from_date" placeholder="From Date" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-4">
                <input type="date" class="form-control" name="to_date" placeholder="To Date" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="purchasesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>User</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                    <tr>
                        <td>#{{ $purchase->id }}</td>
                        <td>{{ $purchase->purchase_date->format('d M Y') }}</td>
                        <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                        <td>{{ $purchase->items->count() }}</td>
                        <td><strong>Tsh {{ number_format($purchase->total_amount, 0) }}</strong></td>
                        <td>{{ $purchase->user->name }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info text-white" 
                                    data-bs-toggle="modal" data-bs-target="#purchaseModal{{ $purchase->id }}">
                                <i class="bi bi-eye"></i> View Details
                            </button>

                            <!-- Purchase Details Modal -->
                            <div class="modal fade" id="purchaseModal{{ $purchase->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Purchase #{{ $purchase->id }} Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <strong>Date:</strong> {{ $purchase->purchase_date->format('d M Y') }}<br>
                                                    <strong>Supplier:</strong> {{ $purchase->supplier->name ?? 'N/A' }}
                                                </div>
                                                <div class="col-md-6 text-end">
                                                    <strong>Created By:</strong> {{ $purchase->user->name }}<br>
                                                    <strong>Total:</strong> Tsh {{ number_format($purchase->total_amount, 0) }}
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>Qty</th>
                                                            <th>Buying Price</th>
                                                            <th>Selling Price</th>
                                                            <th>Batch</th>
                                                            <th>Expiry</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($purchase->items as $item)
                                                        <tr>
                                                            <td>{{ $item->product->name }}</td>
                                                            <td>{{ $item->quantity }}</td>
                                                            <td>Tsh {{ number_format($item->buying_price, 0) }}</td>
                                                            <td>Tsh {{ number_format($item->selling_price, 0) }}</td>
                                                            <td>{{ $item->batch_number ?? '-' }}</td>
                                                            <td>{{ $item->expiry_date?->format('d M Y') ?? '-' }}</td>
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
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No purchases found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#purchasesTable').DataTable({
        pageLength: 25,
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: -1 } // Disable sorting on action column (View Details)
        ],
        language: {
            search: "Search purchases:",
            lengthMenu: "Show _MENU_ purchases per page",
            info: "Showing _START_ to _END_ of _TOTAL_ purchases"
        }
    });
});
</script>
@endpush
