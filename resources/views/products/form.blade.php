@extends('layouts.app')

@section('title', $product ? 'Edit Product' : 'Add Product')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $product ? 'Edit' : 'Add' }} Product</h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $product ? route('products.update', $product) : route('products.store') }}">
            @csrf
            @if($product)
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" class="form-control" name="name" 
                           value="{{ old('name', $product->name ?? '') }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Category</label>
                    <select class="form-select" name="category_id" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" 
                                {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Selling Price</label>
                    <input type="number" class="form-control" name="selling_price" 
                           value="{{ old('selling_price', $product->selling_price ?? '') }}" 
                           step="0.01" min="0" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Reorder Level</label>
                    <input type="number" class="form-control" name="reorder_level" 
                           value="{{ old('reorder_level', $product->reorder_level ?? 10) }}" 
                           min="0" required>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3">{{ old('description', $product->description ?? '') }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                {{ $product ? 'Update' : 'Create' }} Product
            </button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
