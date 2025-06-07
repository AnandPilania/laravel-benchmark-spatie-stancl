<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory, UsesTenantConnection;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getTotalPriceAttribute()
    {
        return $this->quantity * $this->price;
    }

    public function scopeWithProductDetails($query)
    {
        return $query->with('product:id,name,price');
    }

    public function scopeWithOrderDetails($query)
    {
        return $query->with('order:id,customer_name,order_date');
    }

    public function scopeWithProductAndOrderDetails($query)
    {
        return $query->with(['product:id,name,price', 'order:id,customer_name,order_date']);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeHighValue($query, $threshold = 100)
    {
        return $query->where('price', '>', $threshold);
    }

    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->whereHas('product', function ($q) use ($threshold) {
            $q->where('stock_quantity', '<=', $threshold);
        });
    }
}
