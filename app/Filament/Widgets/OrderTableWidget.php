<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OrderTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Orders';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()->where('account_id', session('model_id'))
            )
            ->groups([
                Tables\Grouping\Group::make('status'),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::Modal)
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->searchable()
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->icon('heroicon-s-printer')
                    ->openUrlInNewTab()
                    ->iconButton(),
                Tables\Actions\Action::make('edit')
                    ->fillForm(fn ($record) => array_merge($record->toArray(), ['items' => $record->items->toArray()]))
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
                    ->action(function (array $data, $record) {
                        $order = $record;

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

                        foreach ($record->items as $oldItems) {
                            $product = Product::find($oldItems->product_id);
                            $product->deposit($oldItems->quantity);
                            $oldItems->delete();
                        }

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

                        Notification::make()
                            ->title('Success')
                            ->body('Data saved successfully!')
                            ->success()
                            ->send();
                    })
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->iconButton(),
            ])
            ->defaultSort('created_at', 'desc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->description(fn ($record) => $record->created_at->diffForHumans())
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('uuid')
                    ->description(fn ($record) => $record->type . ' by ' . $record->user?->name)
                    ->label('UUID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('shipping')
                    ->money(locale: 'en', currency: 'eur')
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->money(locale: 'en', currency: 'eur'))
                    ->money(locale: 'en', currency: 'eur')
                    ->color('success')
                    ->sortable(),
            ]);
    }
}
