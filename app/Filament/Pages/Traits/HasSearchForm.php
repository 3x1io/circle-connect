<?php

namespace App\Filament\Pages\Traits;

use App\Models\Account;
use Filament\Forms\Form;
use Filament\Forms;

trait HasSearchForm
{
    public array $search = [];

    public function mount()
    {
        $this->form->fill();

        if (request()->has('search') && ! empty(request()->get('search'))) {
            $this->getAccount = Account::query()
                ->where('phone', 'LIKE', '%' . request()->get('search') . '%')
                ->orWhere('email', 'LIKE', '%' . request()->get('search') . '%')
                ->first();

            if ($this->getAccount) {
                $this->loadAccount();
            } else {
                $this->getAccount = Account::query()->create([
                    'phone' => request()->get('search'),
                    'username' => request()->get('search'),
                    'type' => 'lead',
                ]);

                session()->put('model_id', $this->getAccount->id);
                session()->put('model_type', get_class($this->getAccount));
            }
        }
    }


    public function accountSearchForm(Form $form): Form
    {
        return $form->statePath('search')
            ->schema([
                Forms\Components\TextInput::make('search')
                    ->columnSpan(8)
                    ->label('Search')
                    ->suffixAction(Forms\Components\Actions\Action::make('accountSearchAction')

                        ->label('Search')
                        ->icon('heroicon-o-magnifying-glass')
                        ->action(function () {
                            $this->searchAction();
                        }))
                    ->placeholder('Search by phone or email')
                    ->required()
            ]);
    }

    public function searchAction(): void
    {
        redirect(url('/admin') . '?search=' . $this->accountSearchForm->getState()['search']);
    }
}
