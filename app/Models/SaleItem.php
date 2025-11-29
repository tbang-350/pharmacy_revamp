<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'batch_id',
        'quantity',
        'unit_price',
        'discount',
        'total',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relationships
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(ProductBatch::class, 'batch_id');
    }

    // Boot method to automatically deduct stock from batch
    protected static function booted()
    {
        static::created(function ($saleItem) {
            // Deduct quantity from the batch
            $batch = ProductBatch::find($saleItem->batch_id);
            if ($batch) {
                $batch->decrement('quantity', $saleItem->quantity);
            }
        });
    }
}
