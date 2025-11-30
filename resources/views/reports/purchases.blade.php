@extends('layouts.app')

@section('title', 'Purchase Report')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Purchase Report</h1>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" class="form-control" name="from_date" value="{{ request('from_date', $fromDate->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" class="form-control" name="to_date" value="{{ request('to_date', $toDate->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Supplier</label>
                <select class="form-select" name="supplier_id">
                    <option value="">All Suppliers</option>
                    @foreach(\App\Models\Supplier::all() as $supplier)
                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-3">
    <div class="col-md-6">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6>Total Purchases</h6>
                <h3>Tsh {{ number_format($totalPurchases, 0) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6>Items Purchased</h6>
                <h3>{{ $totalItems }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Purchases Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm" id="purchasesReportTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>User</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->purchase_date->format('d M Y') }}</td>
                        <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                        <td>{{ $purchase->items->count() }}</td>
                        <td><strong>Tsh {{ number_format($purchase->total_amount, 0) }}</strong></td>
                        <td>{{ $purchase->user->name }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-info" 
                                    data-bs-toggle="modal" data-bs-target="#reportPurchaseModal{{ $purchase->id }}">
                                <i class="bi bi-eye"></i>
                            </button>

                            <!-- Purchase Details Modal -->
                            <div class="modal fade" id="reportPurchaseModal{{ $purchase->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Purchase #{{ $purchase->id }} Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>Qty</th>
                                                            <th>Buying Price</th>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($purchase->items as $item)
                                                        <tr>
                                                            <td>{{ $item->product->name }}</td>
                                                            <td>{{ $item->quantity }}</td>
                                                            <td>Tsh {{ number_format($item->buying_price, 0) }}</td>
                                                            <td>Tsh {{ number_format($item->total_cost, 0) }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#purchasesReportTable').DataTable({
        pageLength: 25,
        order: [[0, 'desc']], // Sort by Date descending
        columnDefs: [
            { orderable: false, targets: -1 } // Disable sorting on Action column
        ],
        language: {
            search: "Search report:",
            lengthMenu: "Show _MENU_ records per page",
            info: "Showing _START_ to _END_ of _TOTAL_ records"
        }
    });
});
</script>
@endpush
