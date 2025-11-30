<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->withCount('batches')->paginate(20);
        
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.form', compact('categories'))->with('product', null);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'selling_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.form', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'selling_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }

    public function stock()
    {
        $products = Product::with(['category', 'batches' => function($q) {
            $q->where('quantity', '>', 0)->orderBy('expiry_date', 'asc');
        }])->paginate(50);

        // Flag low stock
        $products->getCollection()->transform(function($product) {
            $product->is_low_stock = $product->current_stock <= $product->reorder_level;
            return $product;
        });

        return view('products.stock', compact('products'));
    }

    public function updatePrice(Request $request, Product $product)
    {
        $request->validate([
            'selling_price' => 'required|numeric|min:0',
        ]);

        $product->update(['selling_price' => $request->selling_price]);

        return response()->json(['success' => true, 'message' => 'Price updated successfully']);
    }
}
