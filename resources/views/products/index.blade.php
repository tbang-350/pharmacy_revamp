@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Products</h1>
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Product
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Selling Price</th>
                        <th>Stock</th>
                        <th>Reorder Level</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name }}</td>
                        <td>Tsh {{ number_format($product->selling_price, 0) }}</td>
                        <td>
                            <span class="badge {{ $product->current_stock <= $product->reorder_level ? 'bg-danger' : 'bg-success' }}">
                                {{ $product->current_stock }}
                            </span>
                        </td>
                        <td>{{ $product->reorder_level }}</td>
                        <td>
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No products found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $products->links() }}
    </div>
</div>
@endsection
