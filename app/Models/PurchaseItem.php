<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'buying_price',
        'selling_price',
        'batch_number',
        'expiry_date',
    ];

    protected $casts = [
        'buying_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    // Relationships
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Boot method to automatically create/update product batches
    protected static function booted()
    {
        static::created(function ($purchaseItem) {
            // Create a product batch when purchase item is created
            ProductBatch::create([
                'product_id' => $purchaseItem->product_id,
                'batch_number' => $purchaseItem->batch_number,
                'quantity' => $purchaseItem->quantity,
                'buying_price' => $purchaseItem->buying_price,
                'expiry_date' => $purchaseItem->expiry_date,
                'purchase_id' => $purchaseItem->purchase_id,
            ]);

            // Update product selling price if provided
            if ($purchaseItem->selling_price) {
                Product::where('id', $purchaseItem->product_id)
                    ->update(['selling_price' => $purchaseItem->selling_price]);
            }
        });
    }
}
