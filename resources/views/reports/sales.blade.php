@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Sales Report</h1>
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
                <label class="form-label">Payment Method</label>
                <select class="form-select" name="payment_method">
                    <option value="">All</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="bank" {{ request('payment_method') == 'bank' ? 'selected' : '' }}>Bank</option>
                    <option value="mobile_money" {{ request('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
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
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6>Total Sales</h6>
                <h3>Tsh {{ number_format($totalSales, 0) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h6>Total Discount</h6>
                <h3>UGX {{ number_format($totalDiscount, 0) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6>Items Sold</h6>
                <h3>{{ $totalItems }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Sales Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Subtotal</th>
                        <th>Discount</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Cashier</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                    <tr>
                        <td>{{ $sale->sale_date->format('d M Y H:i') }}</td>
                        <td>{{ $sale->items->count() }}</td>
                        <td>UGX {{ number_format($sale->subtotal, 0) }}</td>
                        <td>UGX {{ number_format($sale->discount_amount, 0) }}</td>
                        <td><strong>Tsh {{ number_format($sale->total_amount, 0) }}</strong></td>
                        <td>{{ $sale->payment_method_label }}</td>
                        <td>{{ $sale->user->name }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-info" 
                                    data-bs-toggle="modal" data-bs-target="#reportSaleModal{{ $sale->id }}">
                                <i class="bi bi-eye"></i>
                            </button>

                            <!-- Sale Details Modal -->
                            <div class="modal fade" id="reportSaleModal{{ $sale->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Sale #{{ $sale->id }} Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
