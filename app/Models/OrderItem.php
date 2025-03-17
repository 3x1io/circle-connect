<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    public $fillable = [
        'order_id', 'product_id', 'item', 'quantity', 'price','total', 'discount', 'vat', 'shipping',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function total(): string
    {
        return ($this->price + $this->vat) - $this->discount;
    }
}
