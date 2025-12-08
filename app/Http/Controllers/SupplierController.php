<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('purchases')->latest()->paginate(20);
        return view('suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        Supplier::create($request->all());

        return back()->with('success', 'Supplier created successfully');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        $supplier->update($request->all());

        return back()->with('success', 'Supplier updated successfully');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return back()->with('success', 'Supplier deleted successfully');
    }

    public function getAll()
    {
        return response()->json(Supplier::all());
    }
}
