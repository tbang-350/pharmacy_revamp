<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('supplier', 'items.product', 'user');

        if ($request->filled('from_date')) {
            $query->whereDate('purchase_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('purchase_date', '<=', $request->to_date);
        }

        $purchases = $query->latest()->paginate(20);

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $categories = Category::all();
        
        return view('purchases.create', compact('suppliers', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_date' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.name' => 'required_without:items.*.product_id|string',
            'items.*.category_id' => 'required_without:items.*.product_id|exists:categories,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.buying_price' => 'required|numeric|min:0',
            'items.*.selling_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        
        try {
            // Create purchase
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'total_amount' => 0,
                'user_id' => Auth::id(),
                'notes' => $request->notes,
            ]);

            $totalAmount = 0;

            foreach ($request->items as $item) {
                // Create product if it doesn't exist
                if (empty($item['product_id'])) {
                    $product = Product::create([
                        'name' => $item['name'],
                        'category_id' => $item['category_id'],
                        'selling_price' => $item['selling_price'],
                        'reorder_level' => 10,
                    ]);
                    $item['product_id'] = $product->id;
                }

                // Create purchase item (will auto-create batch via observer)
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'buying_price' => $item['buying_price'],
                    'selling_price' => $item['selling_price'],
                    'batch_number' => $item['batch_number'] ?? null,
                    'expiry_date' => $item['expiry_date'] ?? null,
                ]);

                $totalAmount += $item['buying_price'] * $item['quantity'];
            }

            // Update purchase total
            $purchase->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('purchases.index')->with('success', 'Purchase created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create purchase: ' . $e->getMessage())->withInput();
        }
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
            'purchase_date' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        DB::beginTransaction();
        
        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            array_shift($rows);

            // Create purchase
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'total_amount' => 0,
                'user_id' => Auth::id(),
            ]);

            $totalAmount = 0;

            foreach ($rows as $row) {
                if (empty($row[0])) continue; // Skip empty rows

                $productName = $row[0];
                $categoryName = $row[1] ?? 'Uncategorized';
                $quantity = (int)$row[2];
                $buyingPrice = (float)$row[3];
                $sellingPrice = (float)$row[4];
                $batchNumber = $row[5] ?? null;
                $expiryDate = !empty($row[6]) ? Carbon::parse($row[6])->format('Y-m-d') : null;

                // Find or create category
                $category = Category::firstOrCreate(
                    ['name' => $categoryName],
                    ['description' => '']
                );

                // Find or create product
                $product = Product::where('name', $productName)->first();
                if (!$product) {
                    $product = Product::create([
                        'name' => $productName,
                        'category_id' => $category->id,
                        'selling_price' => $sellingPrice,
                        'reorder_level' => 10,
                    ]);
                }

                // Create purchase item
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'buying_price' => $buyingPrice,
                    'selling_price' => $sellingPrice,
                    'batch_number' => $batchNumber,
                    'expiry_date' => $expiryDate,
                ]);

                $totalAmount += $buyingPrice * $quantity;
            }

            $purchase->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('purchases.index')->with('success', 'Products imported successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to import file: ' . $e->getMessage());
        }
    }

    public function searchProduct(Request $request)
    {
        $term = $request->input('term');
        
        $products = Product::with('category')
            ->where('name', 'like', "%{$term}%")
            ->limit(10)
            ->get();

        return response()->json($products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'category_name' => $product->category->name,
                'selling_price' => $product->selling_price,
            ];
        }));
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="purchase_template.csv"',
        ];

        $columns = ['Product Name', 'Category', 'Quantity', 'Buying Price', 'Selling Price', 'Batch Number', 'Expiry Date (YYYY-MM-DD)'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            // Add sample row
            fputcsv($file, ['Paracetamol 500mg', 'Painkillers', '100', '500', '800', 'BATCH001', date('Y-m-d', strtotime('+1 year'))]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
