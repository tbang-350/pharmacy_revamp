@extends('layouts.app')

@section('title', 'Sales History')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Sales History</h1>
    <a href="{{ route('sales.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Sale
    </a>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">From Date</label>
                <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">To Date</label>
                <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Sales Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
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
                    @forelse($sales as $sale)
                    <tr>
                        <td>#{{ $sale->id }}</td>
                        <td>{{ $sale->sale_date->format('d M Y, H:i') }}</td>
                        <td>{{ $sale->items->count() }}</td>
                        <td>Tsh {{ number_format($sale->subtotal, 0) }}</td>
                        <td>Tsh {{ number_format($sale->discount_amount, 0) }}</td>
                        <td><strong>Tsh {{number_format($sale->total_amount, 0) }}</strong></td>
                        <td><span class="badge bg-info">{{ $sale->payment_method_label }}</span></td>
                        <td>{{ $sale->user->name }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info text-white" 
                                    data-bs-toggle="modal" data-bs-target="#saleModal{{ $sale->id }}">
                                <i class="bi bi-eye"></i> View Items
                            </button>

                            <!-- Sale Details Modal -->
                            <div class="modal fade" id="saleModal{{ $sale->id }}" tabindex="-1">
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
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                                            <td>Tsh {{ number_format($sale->subtotal, 0) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" class="text-end"><strong>Discount:</strong></td>
                                                            <td>Tsh {{ number_format($sale->discount_amount, 0) }}</td>
                                                        </tr>
                                                        <tr class="table-active">
                                                            <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                                                            <td><strong>Tsh {{ number_format($sale->total_amount, 0) }}</strong></td>
                                                        </tr>
                                                    </tfoot>
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
                        <td colspan="9" class="text-center">No sales found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $sales->links() }}
    </div>
</div>
@endsection
