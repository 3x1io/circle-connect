<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public $fillable = [
        'user_id',
        'account_id',
        'uuid',
        'status',
        'type',
        'currency',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'zip',
        'company',
        'total',
        'discount',
        'vat',
        'shipping',
    ];

    /**
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * @return string
     */
    public function total(): string
    {
        return number_format(($this->items()->sum('total') + $this->shipping), 2);
    }
}
