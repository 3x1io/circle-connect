<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    public $fillable = [
        'category_id',
        'type',
        'name',
        'description',
        'size',
        'year',
        'stock',
        'sku',
        'price',
        'discount',
        'vat',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function deposit(int $qty = 1): void
    {
        $this->stock += $qty;
        $this->save();
    }

    public function withdraw(int $qty = 1): void
    {
        $this->stock -= $qty;
        $this->save();
    }
}
