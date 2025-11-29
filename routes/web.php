<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Redirect root to dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management (Admin only)
    Route::middleware(['role:Admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });

    // Categories
    Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit']);
    Route::get('categories/all', [CategoryController::class, 'getAll'])->name('categories.all');

    // Suppliers
    Route::resource('suppliers', SupplierController::class)->except(['show', 'create', 'edit']);
    Route::get('suppliers/all', [SupplierController::class, 'getAll'])->name('suppliers.all');

    // Products
    Route::resource('products', ProductController::class);
    Route::get('products-stock', [ProductController::class, 'stock'])->name('products.stock');
    Route::post('products/{product}/update-price', [ProductController::class, 'updatePrice'])->name('products.update-price');

    // Sales
    Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('sales/checkout', [SaleController::class, 'checkout'])->name('sales.checkout');
    Route::get('sales/search-product', [SaleController::class, 'searchProduct'])->name('sales.search-product');
    Route::post('sales/cart/add', [SaleController::class, 'addToCart'])->name('sales.cart.add');
    Route::post('sales/cart/update', [SaleController::class, 'updateCart'])->name('sales.cart.update');
    Route::post('sales/cart/remove', [SaleController::class, 'removeFromCart'])->name('sales.cart.remove');
    Route::get('sales/cart', [SaleController::class, 'getCart'])->name('sales.cart.get');
    Route::post('sales/cart/clear', [SaleController::class, 'clearCart'])->name('sales.cart.clear');

    // Purchases
    Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::post('purchases/import-excel', [PurchaseController::class, 'importExcel'])->name('purchases.import-excel');
    Route::get('purchases/search-product', [PurchaseController::class, 'searchProduct'])->name('purchases.search-product');

    // Reports
    Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/purchases', [ReportController::class, 'purchases'])->name('reports.purchases');
    Route::get('reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
});
