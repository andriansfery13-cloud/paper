<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'discount_percent',
        'tax_percent',
        'tax_amount',
        'subtotal',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->calculateTotals();
        });

        static::saved(function ($item) {
            $item->invoice->calculateTotals()->save();
        });

        static::deleted(function ($item) {
            $item->invoice->calculateTotals()->save();
        });
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function calculateTotals()
    {
        $basePrice = $this->quantity * $this->unit_price;
        $discountAmount = $basePrice * ($this->discount_percent / 100);
        $priceAfterDiscount = $basePrice - $discountAmount;

        $this->tax_amount = $priceAfterDiscount * ($this->tax_percent / 100);
        $this->subtotal = $priceAfterDiscount;

        return $this;
    }

    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
}
