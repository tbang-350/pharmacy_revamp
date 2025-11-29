<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Daily sales
        $dailySales = Sale::whereDate('sale_date', Carbon::today())
            ->sum('total_amount');

        // Total sales (last 30 days)
        $monthlySales = Sale::whereDate('sale_date', '>=', Carbon::now()->subDays(30))
            ->sum('total_amount');

        // Low stock products
        $lowStockCount = Product::whereHas('batches')->get()->filter(function($product) {
            return $product->current_stock <= $product->reorder_level;
        })->count();

        // Products expiring soon (within 3 months)
        $expiringProducts = ProductBatch::expiringSoon(3)
            ->with('product')
            ->get();

        // Recent sales
        $recentSales = Sale::with('items.product')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'dailySales',
            'monthlySales',
            'lowStockCount',
            'expiringProducts',
            'recentSales'
        ));
    }
}
