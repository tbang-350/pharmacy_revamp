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
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>User</th>
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
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No purchases found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $purchases->links() }}
    </div>
</div>
@endsection
