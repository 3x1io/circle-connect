<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    public $fillable = [
        'order_id', 'product_id', 'item', 'quantity', 'total', 'discount', 'vat', 'shipping',
    ];

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return string
     */
    public function total(): string
    {
        return ($this->price + $this->vat) - $this->discount;
    }
}
