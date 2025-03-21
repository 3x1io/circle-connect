<?php

namespace App\Filament\Pages\Traits;

use App\Models\Account;
use Filament\Actions\Action;
use Livewire\Attributes\On;

trait HasEchoEvents
{
    public ?string $eventPhone = null;

    public ?string $eventName = null;

    #[On('echo:call,IncomingCall')]
    public function getCustomer($event): void
    {
        if ($event['user'] == auth()->user()->id) {
            $this->eventPhone = $event['phone'];
            $account = Account::query()
                ->where('phone', 'LIKE', '%' . $this->eventPhone . '%')
                ->orWhere('name', 'LIKE', '%' . $this->eventPhone . '%')
                ->orWhere('email', 'LIKE', '%' . $this->eventPhone . '%')
                ->first();

            if ($account) {
                $this->eventName = $account->meta('letter_salutation') . ' ' . $account->meta('last_name');
            }

            $this->dispatch('open-modal', id: 'call');
        }
    }

    public function redirectToCallAction(): Action
    {
        return Action::make('redirectToCallAction')
            ->label('Accept')
            ->icon('heroicon-s-check-circle')
            ->color('success')
            ->action(function () {
                $this->getAccount = Account::query()
                    ->where('phone', 'LIKE', '%' . $this->eventPhone . '%')
                    ->orWhere('name', 'LIKE', '%' . $this->eventPhone . '%')
                    ->orWhere('email', 'LIKE', '%' . $this->eventPhone . '%')
                    ->first();

                if ($this->getAccount) {
                    $this->getAccount->accountMeta()->create([
                        'user_id' => auth()->user()->id,
                        'type' => 'action',
                        'key' => 'call',
                        'key_value' => $this->eventPhone,
                        'response' => 'ok',
                        'date' => now()->toDateString(),
                        'time' => now()->toTimeString(),
                    ]);

                    $this->redirect(self::getUrl() . '?search=' . $this->eventPhone);
                }
            });
    }
}
