<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $query = Sale::with('items.product', 'user');

        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth());
        $toDate = $request->input('to_date', Carbon::now()->endOfMonth());

        $query->whereBetween('sale_date', [$fromDate, $toDate]);

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $sales = $query->get();
        
        $totalSales = $sales->sum('total_amount');
        $totalDiscount = $sales->sum('discount_amount');
        $totalItems = $sales->sum(function($sale) {
            return $sale->items->sum('quantity');
        });

        return view('reports.sales', compact('sales', 'totalSales', 'totalDiscount', 'totalItems', 'fromDate', 'toDate'));
    }

    public function purchases(Request $request)
    {
        $query = Purchase::with('supplier', 'items.product', 'user');

        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth());
        $toDate = $request->input('to_date', Carbon::now()->endOfMonth());

        $query->whereBetween('purchase_date', [$fromDate, $toDate]);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $purchases = $query->get();
        
        $totalPurchases = $purchases->sum('total_amount');
        $totalItems = $purchases->sum(function($purchase) {
            return $purchase->items->sum('quantity');
        });

        return view('reports.purchases', compact('purchases', 'totalPurchases', 'totalItems', 'fromDate', 'toDate'));
    }

    public function stock()
    {
        $products = Product::with(['category', 'batches' => function($q) {
            $q->where('quantity', '>', 0);
        }])->get();

        $lowStock = $products->filter(function($product) {
            return $product->current_stock <= $product->reorder_level;
        });

        $expiringBatches = ProductBatch::expiringSoon(3)->with('product')->get();

        return view('reports.stock', compact('products', 'lowStock', 'expiringBatches'));
    }
}
