<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_date',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'payment_method',
        'user_id',
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getPaymentMethodLabelAttribute()
    {
        return match($this->payment_method) {
            'cash' => 'Cash',
            'bank' => 'Bank Transfer',
            'mobile_money' => 'Mobile Money',
            default => 'Unknown'
        };
    }
}
