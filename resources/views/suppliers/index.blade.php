@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Suppliers</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#supplierModal">
        <i class="bi bi-plus-circle"></i> Add Supplier
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Purchases</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->contact_person }}</td>
                        <td>{{ $supplier->phone }}</td>
                        <td>{{ $supplier->email }}</td>
                        <td>{{ $supplier->purchases_count }}</td>
                        <td>
                            <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Delete this supplier?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No suppliers found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $suppliers->links() }}
    </div>
</div>

<!-- Add Supplier Modal -->
<div class="modal fade" id="supplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('suppliers.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Person</label>
                        <input type="text" class="form-control" name="contact_person">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
