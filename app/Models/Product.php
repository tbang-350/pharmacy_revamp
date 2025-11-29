<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category_id',
        'description',
        'selling_price',
        'reorder_level',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function batches()
    {
        return $this->hasMany(ProductBatch::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    // Accessors
    public function getCurrentStockAttribute()
    {
        return $this->batches()->sum('quantity');
    }

    public function getBatchesAvailableAttribute()
    {
        return $this->batches()
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
