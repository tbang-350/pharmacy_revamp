<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProductBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'batch_number',
        'quantity',
        'buying_price',
        'expiry_date',
        'purchase_id',
    ];

    protected $casts = [
        'buying_price' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class, 'batch_id');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopeExpiringSoon($query, $months = 3)
    {
        return $query->where('expiry_date', '<=', Carbon::now()->addMonths($months))
                    ->where('expiry_date', '>=', Carbon::now());
    }
}
