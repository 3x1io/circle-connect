<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountMeta extends Model
{
    public $fillable = [
        'account_id',
        'user_id',
        'model_id',
        'model_type',
        'type',
        'key',
        'response',
        'value',
        'key_value',
        'date',
        'time',
    ];

    public $casts = [
        'value' => 'json',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
