<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->paginate(20);
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
        ]);

        Category::create($request->all());

        return back()->with('success', 'Category created successfully');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($request->all());

        return back()->with('success', 'Category updated successfully');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Cannot delete category with products');
        }

        $category->delete();
        return back()->with('success', 'Category deleted successfully');
    }

    public function getAll()
    {
        return response()->json(Category::all());
    }
}
