<?php

namespace App\Filament\Pages\Traits;

use App\Models\Order;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use TomatoPHP\FilamentDocs\Models\Document;
use TomatoPHP\FilamentDocs\Models\DocumentTemplate;

trait HasHeaderActions
{
    public array $setActions = [];

    public function getHeaderActions(): array
    {
        return [
            Action::make('getDocActions')
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
                            ->label('Type')
                            ->searchable()
                            ->preload()
                            ->options([
                                'pending' => 'Pending',
                                'subscription' => 'Subscription',
                                'hospices' => 'Hospices',
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
                }),
        ];
    }

    public function firePrintDocument(int $id, string $type = 'document')
    {
        if ($type === 'document') {
            $item = DocumentTemplate::query()->find($id);
            $record = $this->getAccount;
            $collectAddress = '';
            $collectName = '';

            $collectAddress .= '<div>' . $record->meta('company') . '</div>';
            $collectAddress .= '<div>' . $record->meta('letter_salutation') . ' ' . $record->meta('first_name') . ' ' . $record->meta('last_name') . '</div>';
            $collectAddress .= '<div>' . $record->meta('street') . ' ' . $record->meta('number') . '</div>';
            $collectAddress .= '<div>' . $record->meta('postcode') . ' ' . $record->meta('city') . '</div>';

            $collectName .= $record->meta('letter_salutation') . ' ' . $record->meta('last_name');

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
        }
    }
}
