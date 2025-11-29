<?php

namespace Database\Seeders;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $supplier = Supplier::first();
        $user = User::first();
        $products = Product::all();

        if ($products->isEmpty() || !$user) {
            $this->command->warn('Products or users not found. Run ProductSeeder first.');
            return;
        }

        // Create 3 purchases with different dates
        $purchaseDates = [
            Carbon::now()->subDays(30), // Last month
            Carbon::now()->subDays(15), // 2 weeks ago
            Carbon::now()->subDays(5),  // 5 days ago
        ];

        foreach ($purchaseDates as $index => $purchaseDate) {
            $purchase = Purchase::create([
                'supplier_id' => $supplier?->id,
                'purchase_date' => $purchaseDate,
                'total_amount' => 0,
                'user_id' => $user->id,
                'notes' => 'Sample purchase ' . ($index + 1),
            ]);

            $totalAmount = 0;
            
            // Add 8-12 random products to each purchase
            $selectedProducts = $products->random(rand(8, 12));
            
            foreach ($selectedProducts as $product) {
                $quantity = rand(20, 100);
                $buyingPrice = $product->selling_price * 0.6; // 40% margin
                
                // Different expiry dates for variety
                $expiryMonths = rand(6, 24); // 6 months to 2 years
                $expiryDate = Carbon::now()->addMonths($expiryMonths);
                
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'buying_price' => $buyingPrice,
                    'selling_price' => $product->selling_price,
                    'batch_number' => 'BATCH-' . strtoupper(substr(md5($product->id . $index), 0, 8)),
                    'expiry_date' => $expiryDate,
                ]);

                $totalAmount += $buyingPrice * $quantity;
            }

            $purchase->update(['total_amount' => $totalAmount]);
        }

        $this->command->info('Created 3 purchases with stock for ' . $products->count() . ' products.');
    }
}
