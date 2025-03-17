<?php

namespace App\Filament\Pages\Traits;

use App\Models\Account;
use Livewire\Attributes\On;

trait HasEchoEvents
{
    #[On('echo:call,IncomingCall')]
    public function getCustomer($event): void
    {
        if ($event['user'] == auth()->user()->id) {
            $this->getAccount = Account::query()
                ->where('phone', 'LIKE', '%' . $event['phone'] . '%')
                ->orWhere('email', 'LIKE', '%' . $event['phone'] . '%')
                ->first();

            if ($this->getAccount) {
                $this->getAccount->accountMeta()->create([
                    'user_id' => auth()->user()->id,
                    'type' => 'action',
                    'key' => 'call',
                    'key_value' => $event['phone'],
                    'response' => 'ok',
                    'date' => now()->toDateString(),
                    'time' => now()->toTimeString(),
                ]);
                $this->loadAccount();

                $this->js('window.location.reload()');
            }
        }
    }

}
