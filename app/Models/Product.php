<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;
use App\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, LogsActivity;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'code',
        'name',
        'description',
        'unit',
        'purchase_price',
        'selling_price',
        'tax_rate',
        'is_taxable',
        'stock',
        'min_stock',
        'sku',
        'barcode',
        'image',
        'is_active',
        'track_stock',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'is_taxable' => 'boolean',
        'is_active' => 'boolean',
        'track_stock' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (!$product->code) {
                $product->code = static::generateCode($product->tenant_id);
            }
        });
    }

    public static function generateCode($tenantId)
    {
        $lastProduct = static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastProduct ? (int) substr($lastProduct->code, 4) + 1 : 1;
        return 'PRD-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function quotationItems()
    {
        return $this->hasMany(QuotationItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock')
            ->where('track_stock', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getFormattedSellingPriceAttribute()
    {
        return 'Rp ' . number_format($this->selling_price, 0, ',', '.');
    }

    public function getFormattedPurchasePriceAttribute()
    {
        return 'Rp ' . number_format($this->purchase_price, 0, ',', '.');
    }

    public function getPriceWithTaxAttribute()
    {
        if ($this->is_taxable) {
            return $this->selling_price * (1 + $this->tax_rate / 100);
        }
        return $this->selling_price;
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    // Methods
    public function isLowStock()
    {
        return $this->track_stock && $this->stock <= $this->min_stock;
    }

    public function isOutOfStock()
    {
        return $this->track_stock && $this->stock <= 0;
    }

    public function adjustStock($quantity, $type = 'add')
    {
        if (!$this->track_stock) {
            return;
        }

        if ($type === 'add') {
            $this->increment('stock', $quantity);
        } else {
            $this->decrement('stock', $quantity);
        }
    }
}
