@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('sales.create') }}" class="btn btn-primary">
                <i class="bi bi-cart-plus"></i> New Sale
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Today's Sales</h6>
                <h3 class="mb-0">Tsh {{ number_format($dailySales, 0) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Monthly Sales</h6>
                <h3 class="mb-0">Tsh {{ number_format($monthlySales, 0) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="card-title">Low Stock Alert</h6>
                <h3 class="mb-0">{{ $lowStockCount }} Products</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="card-title">Expiring Soon</h6>
                <h3 class="mb-0">{{ $expiringProducts->count() }} Batches</h3>
            </div>
        </div>
    </div>
</div>

<!-- Expiring Products -->
@if($expiringProducts->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Products Expiring Soon (Within 3 Months)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
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
                            @foreach($expiringProducts as $batch)
                            <tr>
                                <td>{{ $batch->product->name }}</td>
                                <td>{{ $batch->batch_number ?? 'N/A' }}</td>
                                <td>{{ $batch->quantity }}</td>
                                <td>{{ $batch->expiry_date?->format('d M Y') ?? 'N/A' }}</td>
                                <td>{{ $batch->expiry_date?->diffInDays(now()) ?? 'N/A' }} days</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Recent Sales -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Sales</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Payment Method</th>
                                <th>Total</th>
                                <th>Cashier</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                            <tr>
                                <td>{{ $sale->sale_date->format('d M Y, H:i') }}</td>
                                <td>{{ $sale->items->count() }} items</td>
                                <td><span class="badge bg-info">{{ $sale->payment_method_label }}</span></td>
                                <td>Tsh {{ number_format($sale->total_amount, 0) }}</td>
                                <td>{{ $sale->user->name }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" data-bs-target="#dashboardSaleModal{{ $sale->id }}">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <!-- Sale Details Modal -->
                                    <div class="modal fade" id="dashboardSaleModal{{ $sale->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Sale #{{ $sale->id }} Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <strong>Date:</strong> {{ $sale->sale_date->format('d M Y, H:i') }}<br>
                                                            <strong>Cashier:</strong> {{ $sale->user->name }}
                                                        </div>
                                                        <div class="col-md-6 text-end">
                                                            <strong>Payment:</strong> {{ $sale->payment_method_label }}<br>
                                                            <strong>Total:</strong> Tsh {{ number_format($sale->total_amount, 0) }}
                                                        </div>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Product</th>
                                                                    <th>Qty</th>
                                                                    <th>Price</th>
                                                                    <th>Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($sale->items as $item)
                                                                <tr>
                                                                    <td>{{ $item->product->name }}</td>
                                                                    <td>{{ $item->quantity }}</td>
                                                                    <td>Tsh {{ number_format($item->unit_price, 0) }}</td>
                                                                    <td>Tsh {{ number_format($item->total, 0) }}</td>
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
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No recent sales</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
