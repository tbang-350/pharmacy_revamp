@extends('layouts.app')

@section('title', 'Stock Report')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Stock Report</h1>
</div>

<!-- Summary Cards -->
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6>Total Products</h6>
                <h3>{{ $products->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h6>Low Stock Items</h6>
                <h3>{{ $lowStock->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6>Expiring Batches (Soon)</h6>
                <h3>{{ $expiringBatches->count() }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Stock Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover" id="stockReportTable">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Current Stock</th>
                        <th>Reorder Level</th>
                        <th>Status</th>
                        <th>Value (Tsh)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr class="{{ $product->is_low_stock ? 'table-warning' : '' }}">
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name }}</td>
                        <td>{{ $product->current_stock }}</td>
                        <td>{{ $product->reorder_level }}</td>
                        <td>
                            @if($product->is_low_stock)
                                <span class="badge bg-warning text-dark">Low Stock</span>
                            @else
                                <span class="badge bg-success">Good</span>
                            @endif
                        </td>
                        <td>{{ number_format($product->current_stock * $product->selling_price, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Expiring Batches Section -->
@if($expiringBatches->count() > 0)
<div class="card mt-4">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">Expiring Batches (Next 3 Months)</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm" id="expiringBatchesTable">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Batch Number</th>
                        <th>Quantity</th>
                        <th>Expiry Date</th>
                        <th>Days Remaining</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expiringBatches as $batch)
                    <tr>
                        <td>{{ $batch->product->name }}</td>
                        <td>{{ $batch->batch_number }}</td>
                        <td>{{ $batch->quantity }}</td>
                        <td>{{ $batch->expiry_date->format('d M Y') }}</td>
                        <td>
                            @php
                                $days = now()->diffInDays($batch->expiry_date, false);
                            @endphp
                            <span class="badge {{ $days < 30 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                {{ round($days) }} days
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#stockReportTable').DataTable({
        pageLength: 25,
        order: [[2, 'asc']], // Sort by Stock ascending (to see low stock first)
        language: {
            search: "Search stock:",
            lengthMenu: "Show _MENU_ records per page",
            info: "Showing _START_ to _END_ of _TOTAL_ records"
        }
    });

    @if($expiringBatches->count() > 0)
    $('#expiringBatchesTable').DataTable({
        pageLength: 10,
        order: [[3, 'asc']], // Sort by Expiry Date ascending
        language: {
            search: "Search batches:",
            lengthMenu: "Show _MENU_ records per page",
            info: "Showing _START_ to _END_ of _TOTAL_ records"
        }
    });
    @endif
});
</script>
@endpush
