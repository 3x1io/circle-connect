<?php

namespace App\Filament\Pages\Traits;

use App\Models\Account;

trait CanLoadAccount
{
    public ?Account $getAccount = null;

    public function loadAccount(): void
    {
        $this->getAccount->checkMeta();

        if ($this->getAccount->phone && empty($this->getAccount->meta('tel_mobile'))) {
            $this->getAccount->accountMeta()->where('key', 'tel_mobile')->first()->update([
                'key_value' => $this->getAccount->phone,
            ]);
        }

        if ($this->getAccount->email && empty($this->getAccount->meta('email'))) {
            $this->getAccount->accountMeta()->where('key', 'email')->first()->update([
                'key_value' => $this->getAccount->email,
            ]);
        }

        if ($this->getAccount->name && (empty($this->getAccount->meta('first_name')) && empty($this->getAccount->meta('last_name')))) {
            $this->getAccount->accountMeta()->where('key', 'first_name')->first()->update([
                'key_value' => str($this->getAccount->name)->explode(' ')[0],
            ]);

            $this->getAccount->accountMeta()->where('key', 'last_name')->first()->update([
                'key_value' => str($this->getAccount->name)->explode(' ')[1],
            ]);
        }

        $this->data = $this->getAccount->loadData()->map(function ($item) {
            if (str($item->key)->contains(['items', 'canceling_reasons'])) {
                if ($item->value) {
                    $item->key_value = $item->value;
                } else {
                    $item->key_value = [];
                }
            }

            return $item;
        })->pluck('key_value', 'key')->toArray();

        session()->put('model_id', $this->getAccount->id);
        session()->put('model_type', get_class($this->getAccount));
    }
}
