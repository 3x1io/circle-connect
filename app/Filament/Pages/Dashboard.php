<?php

namespace App\Filament\Pages;

use App\Models\Account;
use App\Models\Order;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\On;
use TomatoPHP\FilamentDocs\Models\Document;
use TomatoPHP\FilamentDocs\Models\DocumentTemplate;

class Dashboard extends \Filament\Pages\Dashboard implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $view = 'filament.pages.dashboard';

    public array $data = [];

    public array $search = [];

    public array $setActions = [];

    public ?Account $getAccount = null;

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

    public function getTitle(): string | Htmlable
    {
        return $this->getAccount ? (new HtmlString($this->getAccount->name . ' <div class="text-sm font-bold" >+' . $this->getAccount->phone . '</div>')) : 'System';
    }

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

    public function mount()
    {
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

    protected function getForms(): array
    {
        return [
            'form', 'accountSearchForm',
        ];
    }

    public function accountSearchForm(Form $form): Form
    {
        return $form->statePath('search')
            ->schema([
                Forms\Components\Section::make('Search For Customer / Lead')
                    ->schema([
                        Forms\Components\TextInput::make('search')
                            ->label('Search')
                            ->placeholder('Search by phone or email')
                            ->required(),
                    ]),
            ]);
    }

    public function searchAction(): void
    {
        redirect(url('/admin') . '?search=' . $this->accountSearchForm->getState()['search']);
    }

    public function accountSearchAction()
    {
        return Action::make('accountSearchAction')
            ->label('Search')
            ->icon('heroicon-o-magnifying-glass')
            ->action(function () {
                $this->searchAction();
            });
    }

    public function getHeaderActions(): array
    {
        $actions = [];
        $actions[] = Action::make('getDocActions')
            ->form([
                Forms\Components\Grid::make([
                    'sm' => 1,
                    'lg' => 2,
                ])->schema([
                    Forms\Components\TextInput::make('uuid')
                        ->disabled(fn (Order $order) => $order->exists)
                        ->default(fn () => \Illuminate\Support\Str::random(8))
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('status')
                        ->searchable()
                        ->preload()
                        ->options([
                            'pending' => 'Pending',
                            'success' => 'Success',
                        ])
                        ->required()
                        ->default('pending'),
                    Forms\Components\Repeater::make('items')
                        ->columnSpanFull()
                        ->hiddenLabel()
                        ->schema([
                            Forms\Components\Select::make('product_id')
                                ->searchable()
                                ->options(Product::query()->pluck('name', 'id')->toArray())
                                ->live()
                                ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                    $product = Product::find($get('product_id'));
                                    if ($product) {
                                        $set('price', $product->price);
                                        $set('discount', $product->discount);
                                        $set('vat', $product->vat);
                                        $set('total', (($product->price + $product->vat) - $product->discount) * $get('quantity'));
                                    }
                                })
                                ->columnSpan(3),
                            Forms\Components\TextInput::make('quantity')
                                ->columnSpan(2)
                                ->live()
                                ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                    $product = Product::find($get('product_id'));
                                    if ($product) {
                                        $set('price', $product->price);
                                        $set('discount', $product->discount);
                                        $set('vat', $product->vat);
                                        $set('total', (($product->price + $product->vat) - $product->discount) * $get('quantity'));
                                    }
                                })
                                ->default(1)
                                ->numeric(),
                            Forms\Components\TextInput::make('price')
                                ->disabled()
                                ->columnSpan(1)
                                ->default(0)
                                ->numeric(),
                            Forms\Components\TextInput::make('discount')
                                ->disabled()
                                ->columnSpan(1)
                                ->default(0)
                                ->numeric(),
                            Forms\Components\TextInput::make('vat')
                                ->disabled()
                                ->columnSpan(1)
                                ->default(0)
                                ->numeric(),
                            Forms\Components\TextInput::make('total')
                                ->disabled()
                                ->columnSpan(2)
                                ->default(0)
                                ->numeric(),
                        ])
                        ->lazy()
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            $items = $get('items');
                            $total = 0;
                            $discount = 0;
                            $vat = 0;
                            foreach ($items as $orderItem) {
                                $product = Product::find($orderItem['product_id']);
                                if ($product) {
                                    $total += ((($product->price + $product->vat) - $product->discount) * $orderItem['quantity']);
                                    $discount += ($product->discount * $orderItem['quantity']);
                                    $vat += ($product->vat * $orderItem['quantity']);
                                }

                            }
                            $set('total', $total);
                            $set('discount', $discount);
                            $set('vat', $vat);
                        })
                        ->columns(12),
                    Forms\Components\TextInput::make('shipping')
                        ->lazy()
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            $items = $get('items');
                            $total = 0;
                            foreach ($items as $orderItem) {
                                $product = Product::find($orderItem['product_id']);
                                if ($product) {
                                    $total += ((($product->price + $product->vat) - $product->discount) * $orderItem['quantity']);
                                }
                            }

                            $set('total', $total + (int) $get('shipping'));
                        })
                        ->numeric()
                        ->default(0),
                    Forms\Components\TextInput::make('vat')
                        ->disabled()
                        ->numeric()
                        ->default(0),
                    Forms\Components\TextInput::make('discount')
                        ->disabled()
                        ->numeric()
                        ->default(0),
                    Forms\Components\TextInput::make('total')
                        ->disabled()
                        ->numeric()
                        ->default(0),
                ]),
            ])
            ->label('Create Order')
            ->icon('heroicon-o-shopping-cart')
            ->action(function (array $data) {
                foreach ($data['items'] as $item) {
                    $product = Product::find($item['product_id']);
                    if ($product->stock >= $item['quantity']) {
                        $product->withdraw($item['quantity']);
                    } else {
                        Notification::make()
                            ->title('Error')
                            ->body('Not enough stock for product: ' . $product->name)
                            ->danger()
                            ->send();

                        return;
                    }
                }
                $data['user_id'] = auth()->user()->id;
                $data['account_id'] = $this->getAccount->id;
                $order = Order::query()->create(collect($data)->filter(fn ($item, $key) => $key !== 'items')->toArray());

                foreach ($data['items'] as $item) {
                    $product = Product::find($item['product_id']);
                    $product->withdraw($item['quantity']);
                    if ($product) {
                        $item['item'] = $product->name;
                        $item['price'] = $product->price;
                        $item['discount'] = $product->discount;
                        $item['vat'] = $product->vat;
                        $item['total'] = (($product->price + $product->vat) - $product->discount) * $item['quantity'];
                    }
                    $item['order_id'] = $order->id;

                    $order->items()->create($item);
                }

                $order->total = $order->items()->sum('total');
                $order->save();

                $this->notify();

                $this->js('window.location.reload()');
            });

        $docs = DocumentTemplate::query()->where('is_active', 1)->get();

        foreach ($docs as $item) {
            $this->setActions[] = $docs->pluck('name', 'id')->toArray();
            $actions[] = Action::make(str($item->name)->camel()->toString())
                ->requiresConfirmation()
                ->label($item->name)
                ->icon(str($item->icon)->replace('bx ', ''))
                ->action(function () use ($item) {
                    $record = $this->getAccount;
                    $collectAddress = '';
                    $collectName = '';

                    $collectAddress .= $record->meta('company') . "\n";
                    $collectAddress .= $record->meta('letter_salutation') . ' ' . $record->meta('first_name') . ' ' . $record->meta('last_name') . "\n";
                    $collectAddress .= $record->meta('street') . ' ' . $record->meta('number') . "\n";
                    $collectAddress .= $record->meta('postcode') . ' ' . $record->meta('city') . "\n";

                    $collectName .= $record->meta('letter_salutation') . ' ' . $record->meta('first_name');

                    $body = str(DocumentTemplate::query()->find($item->id)->body)
                        ->replace('$ADDRESS', $collectAddress)
                        ->replace('$NAME', $collectName)
                        ->replace('$FAX', $record->meta('fax_number'))
                        ->replace('$DATE', date('d.m.Y'));

                    Document::query()->create([
                        'model_id' => $record->id,
                        'model_type' => get_class($record),
                        'document_template_id' => $item->id,
                        'body' => $body,
                    ]);

                    $this->notify();

                    $this->js('window.location.reload()');
                });
        }

        return [
            ActionGroup::make($actions)
                ->visible(fn () => $this->getAccount)
                ->button()
                ->label('Documents')
                ->icon('heroicon-o-document-text'),
        ];
    }

    public function form(Form $form): Form
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
            ]);
    }

    public function save()
    {
        $this->form->validate();

        $this->data = $this->form->getState();

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

    public function saveAction()
    {
        return Action::make('saveAction')
            ->action(function () {
                $this->save();
            });
    }

    public function notify(): void
    {
        Notification::make()
            ->title('Success')
            ->body('Data saved successfully!')
            ->success()
            ->send();
    }
}
