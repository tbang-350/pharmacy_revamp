<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with('items.product', 'user');

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('sale_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('sale_date', '<=', $request->to_date);
        }

        $sales = $query->latest()->paginate(20);

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        // Fetch top 5 frequently bought items
        $frequentProducts = Product::select('products.*', DB::raw('SUM(sale_items.quantity) as total_sold'))
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return view('sales.create', compact('frequentProducts'));
    }

    public function searchProduct(Request$request)
    {
        $term = $request->input('term');
        
        $products = Product::with(['category', 'batches' => function($q) {
                $q->where('quantity', '>', 0)
                  ->orderBy('expiry_date', 'asc')
                  ->orderBy('created_at', 'asc');
            }])
            ->where('name', 'like', "%{$term}%")
            ->limit(10)
            ->get();

        return response()->json($products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category->name,
                'price' => $product->selling_price,
                'stock' => $product->current_stock,
                'batches' => $product->batches
            ];
        }));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::with('batches_available')->findOrFail($request->product_id);
        
        // Check if sufficient stock
        if ($product->current_stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Available: ' . $product->current_stock
            ], 400);
        }

        // Get or create cart
        $cart = session('cart', []);
        $cartId = uniqid();

        // Add item to cart
        $cart[$cartId] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'quantity' => $request->quantity,
            'price' => $product->selling_price,
            'discount' => 0,
            'total' => $product->selling_price * $request->quantity
        ];

        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'message' => 'Product added to cart'
        ]);
    }

    public function updateCart(Request $request)
    {
        $cart = session('cart', []);
        
        if (isset($cart[$request->cart_id])) {
            $cart[$request->cart_id]['quantity'] = $request->quantity;
            $cart[$request->cart_id]['discount'] = $request->discount ?? 0;
            
            $subtotal = $cart[$request->cart_id]['price'] * $request->quantity;
            $cart[$request->cart_id]['total'] = $subtotal - $cart[$request->cart_id]['discount'];
            
            session(['cart' => $cart]);
            
            return response()->json(['success' => true, 'cart' => $cart]);
        }
        
        return response()->json(['success' => false], 400);
    }

    public function removeFromCart(Request $request)
    {
        $cart = session('cart', []);
        
        if (isset($cart[$request->cart_id])) {
            unset($cart[$request->cart_id]);
            session(['cart' => $cart]);
            
            return response()->json(['success' => true, 'cart' => $cart]);
        }
        
        return response()->json(['success' => false], 400);
    }

    public function getCart()
    {
        return response()->json(['cart' => session('cart', [])]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,bank,mobile_money',
            'overall_discount' => 'nullable|numeric|min:0',
        ]);

        $cart = $request->input('cart', session('cart', []));
        
        if (empty($cart)) {
            return back()->with('error', 'Cart is empty');
        }

        DB::beginTransaction();
        
        try {
            // Calculate totals
            $subtotal = collect($cart)->sum(function($item) {
                return ($item['price'] * $item['quantity']) - ($item['discount'] ?? 0);
            });
            $overallDiscount = $request->overall_discount ?? 0;
            $total = $subtotal - $overallDiscount;

            // Create sale
            $sale = Sale::create([
                'sale_date' => now(),
                'subtotal' => $subtotal,
                'discount_amount' => $overallDiscount,
                'tax_amount' => 0,
                'total_amount' => $total,
                'payment_method' => $request->payment_method,
                'user_id' => Auth::id(),
            ]);

            // Create sale items and deduct stock using FIFO
            foreach ($cart as $item) {
                $product = Product::findOrFail($item['product_id']);
                $remainingQty = $item['quantity'];

                // Get available batches (FIFO - oldest expiry first)
                $batches = $product->batches()
                    ->where('quantity', '>', 0)
                    ->orderBy('expiry_date', 'asc')
                    ->orderBy('created_at', 'asc')
                    ->get();

                foreach ($batches as $batch) {
                    if ($remainingQty <= 0) break;

                    $qtyToDeduct = min($remainingQty, $batch->quantity);

                    // Create sale item
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'batch_id' => $batch->id,
                        'quantity' => $qtyToDeduct,
                        'unit_price' => $item['price'],
                        'discount' => $item['discount'] / $item['quantity'] * $qtyToDeduct,
                        'total' => ($item['price'] * $qtyToDeduct) - ($item['discount'] / $item['quantity'] * $qtyToDeduct),
                    ]);

                    $remainingQty -= $qtyToDeduct;
                }
            }

            // Clear cart
            session()->forget('cart');

            DB::commit();

            return redirect()->route('sales.create')->with('success', 'Sale completed successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to complete sale: ' . $e->getMessage());
        }
    }

    public function clearCart()
    {
        session()->forget('cart');
        return response()->json(['success' => true]);
    }
}
