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

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @param int $qty
     * @return void
     */
    public function deposit(int $qty=1): void
    {
        $this->stock += $qty;
        $this->save();
    }


    /**
     * @param int $qty
     * @return void
     */
    public function withdraw(int $qty=1): void
    {
        $this->stock -= $qty;
        $this->save();
    }
}
