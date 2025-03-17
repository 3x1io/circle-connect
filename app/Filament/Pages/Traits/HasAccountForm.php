<?php

namespace App\Filament\Pages\Traits;

use Filament\Forms;
use Filament\Forms\Form;

trait HasAccountForm
{
    public array $data = [];

    public function accountForm(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Tabs::make()
                    ->schema([
                        Forms\Components\Tabs\Tab::make('Address')
                            ->icon('bxs-map')
                            ->columns(3)
                            ->schema([
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.address.company')),
                                Forms\Components\TextInput::make('company')
                                    ->columnSpan(2)
                                    ->hiddenLabel()
                                    ->placeholder(trans('system.form.address.company')),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.address.salutation') . ' / ' . trans('system.form.address.title')),
                                Forms\Components\TextInput::make('salutation')->hiddenLabel()->placeholder(trans('system.form.address.salutation')),
                                Forms\Components\TextInput::make('title')->hiddenLabel()->placeholder(trans('system.form.address.title')),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.address.first_name') . ' / ' . trans('system.form.address.last_name')),
                                Forms\Components\TextInput::make('first_name')->hiddenLabel()->placeholder(trans('system.form.address.first_name')),
                                Forms\Components\TextInput::make('last_name')->hiddenLabel()->placeholder(trans('system.form.address.last_name')),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.address.street') . ' / ' . trans('system.form.address.number')),
                                Forms\Components\TextInput::make('street')->hiddenLabel()->placeholder(trans('system.form.address.street')),
                                Forms\Components\TextInput::make('number')->hiddenLabel()->numeric()->placeholder(trans('system.form.address.number')),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.address.postcode') . ' / ' . trans('system.form.address.city')),
                                Forms\Components\TextInput::make('postcode')->hiddenLabel()->placeholder(trans('system.form.address.postcode')),
                                Forms\Components\TextInput::make('city')->hiddenLabel()->placeholder(trans('system.form.address.city')),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.address.country')),
                                Forms\Components\TextInput::make('country')->hiddenLabel()->columnSpan(2)->placeholder(trans('system.form.address.country')),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.address.letter_salutation')),
                                Forms\Components\TextInput::make('letter_salutation')->hiddenLabel()->columnSpan(2)->placeholder(trans('system.form.address.letter_salutation')),
                            ]),
                        Forms\Components\Tabs\Tab::make('Bank Account')
                            ->icon('bxs-bank')
                            ->columns(3)
                            ->schema([
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.payments.payment_type')),
                                Forms\Components\Radio::make('payment_type')
                                    ->hiddenLabel()
                                    ->inline()
                                    ->label(trans('system.form.payments.payment_type'))
                                    ->columnSpan(2)
                                    ->options([
                                        'invoice' => 'Invoice',
                                        'credit_card' => 'Credit card',
                                    ]),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.payments.id_number')),
                                Forms\Components\TextInput::make('id_number')
                                    ->hiddenLabel()
                                    ->numeric()
                                    ->placeholder(trans('system.form.payments.id_number'))
                                    ->columnSpan(2),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.payments.direct_debit')),
                                Forms\Components\TextInput::make('direct_debit')
                                    ->hiddenLabel()
                                    ->placeholder(trans('system.form.payments.direct_debit'))
                                    ->columnSpan(2),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.payments.bank_name')),
                                Forms\Components\TextInput::make('bank_name')
                                    ->hiddenLabel()
                                    ->placeholder(trans('system.form.payments.bank_name'))
                                    ->columnSpan(2),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.payments.account_owner')),
                                Forms\Components\TextInput::make('account_owner')
                                    ->hiddenLabel()
                                    ->placeholder(trans('system.form.payments.account_owner'))
                                    ->columnSpan(2),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.payments.iban')),
                                Forms\Components\TextInput::make('iban')
                                    ->hiddenLabel()
                                    ->placeholder(trans('system.form.payments.iban'))
                                    ->columnSpan(2),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.payments.bic')),
                                Forms\Components\TextInput::make('bic')
                                    ->hiddenLabel()
                                    ->placeholder(trans('system.form.payments.bic'))
                                    ->columnSpan(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Credit Card')
                            ->icon('bxs-credit-card')
                            ->columns(3)
                            ->schema([
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.payments.card_type')),
                                Forms\Components\Radio::make('card_type')
                                    ->hiddenLabel()
                                    ->inline()
                                    ->label(trans('system.form.payments.card_type'))
                                    ->columnSpan(2)
                                    ->options([
                                        'mastercard' => 'Mastercard',
                                        'visa' => 'Visa',
                                        'amex' => 'Amex',
                                    ]),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.payments.credit_card_owner')),
                                Forms\Components\TextInput::make('credit_card_owner')
                                    ->hiddenLabel()
                                    ->placeholder(trans('system.form.payments.credit_card_owner'))
                                    ->columnSpan(2),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.payments.credit_card_number')),
                                Forms\Components\TextInput::make('credit_card_number')
                                    ->numeric()
                                    ->hiddenLabel()
                                    ->placeholder(trans('system.form.payments.credit_card_number'))
                                    ->columnSpan(2),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.payments.credit_card_expiry') . ' / ' . trans('system.form.payments.credit_card_cvc')),
                                Forms\Components\TextInput::make('credit_card_expiry')
                                    ->hiddenLabel()
                                    ->placeholder(trans('system.form.payments.credit_card_expiry')),
                                Forms\Components\TextInput::make('credit_card_cvc')
                                    ->numeric()
                                    ->hiddenLabel()
                                    ->placeholder(trans('system.form.payments.credit_card_cvc')),
                            ]),
                        Forms\Components\Tabs\Tab::make('Communication')
                            ->icon('bxs-phone')
                            ->columns(3)
                            ->schema([
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.communication.tel_direct')),
                                Forms\Components\TextInput::make('tel_direct')
                                    ->hiddenLabel()
                                    ->placeholder(trans('system.form.communication.tel_direct'))
                                    ->columnSpan(2),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.communication.tel_secretary')),
                                Forms\Components\TextInput::make('tel_secretary')
                                    ->hiddenLabel()
                                    ->placeholder(trans('system.form.communication.tel_secretary'))
                                    ->columnSpan(2),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.communication.tel_mobile')),
                                Forms\Components\TextInput::make('tel_mobile')
                                    ->hiddenLabel()
                                    ->placeholder(trans('system.form.communication.tel_mobile'))
                                    ->columnSpan(2),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.communication.fax_number')),
                                Forms\Components\TextInput::make('fax_number')
                                    ->hiddenLabel()
                                    ->placeholder(trans('system.form.communication.fax_number'))
                                    ->columnSpan(2),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.communication.email')),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->hiddenLabel()
                                    ->placeholder(trans('system.form.communication.email'))
                                    ->columnSpan(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Additional information')
                            ->icon('bxs-user-circle')
                            ->columns(3)
                            ->schema([
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.additional.important')),
                                Forms\Components\TextInput::make('important')
                                    ->hiddenLabel()
                                    ->placeholder(trans('system.form.additional.important'))
                                    ->columnSpan(2),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.additional.name_of_secretary')),
                                Forms\Components\TextInput::make('name_of_secretary')
                                    ->hiddenLabel()
                                    ->placeholder(trans('system.form.additional.name_of_secretary'))
                                    ->columnSpan(2),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.additional.date_of_birth')),
                                Forms\Components\DatePicker::make('date_of_birth')
                                    ->hiddenLabel()
                                    ->placeholder(trans('system.form.additional.date_of_birth'))
                                    ->columnSpan(2),
                                Forms\Components\Placeholder::make('placeholder')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.additional.evaluation')),
                                Forms\Components\TextInput::make('evaluation')
                                    ->hiddenLabel()
                                    ->placeholder(trans('system.form.additional.evaluation'))
                                    ->columnSpan(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Status')
                            ->icon('bx-money-withdraw')
                            ->columns(3)
                            ->schema([
                                Forms\Components\Placeholder::make('price_per_bottle_max')
                                    ->hiddenLabel()
                                    ->content(trans('system.form.status.price_per_bottle_max')),
                                Forms\Components\TextInput::make('price_per_bottle_max')
                                    ->numeric()
                                    ->hiddenLabel()
                                    ->placeholder(trans('system.form.status.price_per_bottle_max'))
                                    ->columnSpan(2),
                                Forms\Components\CheckboxList::make('items')
                                    ->default([])
                                    ->searchable()
                                    ->hiddenLabel()
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->options([
                                        'red_wine' => trans('system.form.status.red_wine'),
                                        'white_wine' => trans('system.form.status.white_wine'),
                                        'sparkling_wine' => trans('system.form.status.sparkling_wine'),
                                        'france' => trans('system.form.status.france'),
                                        'italy' => trans('system.form.status.italy'),
                                        'spain' => trans('system.form.status.spain'),
                                        'subscription' => trans('system.form.status.subscription'),
                                        'hospice_de_beaune' => trans('system.form.status.hospice_de_beaune'),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Cancellation')
                            ->icon('bxs-x-circle')
                            ->columns(3)
                            ->schema([
                                Forms\Components\Repeater::make('canceling_reasons')
                                    ->label(trans('system.form.cancellation.canceling_reasons'))
                                    ->schema([
                                        Forms\Components\Placeholder::make('reason')
                                            ->hiddenLabel()
                                            ->content(trans('system.form.cancellation.reason')),
                                        Forms\Components\TextInput::make('reason')
                                            ->hiddenLabel()
                                            ->placeholder(trans('system.form.cancellation.reason'))
                                            ->columnSpan(2),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ]),
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('saveAction')
                        ->action(function () {
                            $this->save();
                        }),
                ]),
            ]);
    }

    public function save(): void
    {
        $this->accountForm->validate();

        $this->data = $this->accountForm->getState();

        foreach ($this->data as $key => $value) {
            $saveData = [];
            if (str($key)->contains(['items', 'canceling_reasons'])) {
                $saveData['value'] = $value ?? [];
            } else {
                $saveData['key_value'] = $value;
            }

            $this->getAccount->accountMeta()->where('key', $key)->update($saveData);
        }

        $this->notify();
    }
}
