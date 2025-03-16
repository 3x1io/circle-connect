<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'bxs-box';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->columnSpanFull()
                    ->searchable()
                    ->preload()
                    ->relationship('category', 'name')
                    ->required(),
                Forms\Components\TextInput::make('stock')
                    ->columnSpanFull()
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('sku')
                    ->columnSpanFull()
                    ->label('SKU')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('type'),

                Forms\Components\TextInput::make('size')
                    ->numeric(),
                Forms\Components\TextInput::make('year')
                    ->numeric(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('discount')
                    ->numeric(),
                Forms\Components\TextInput::make('vat')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->badge()
                    ->icon(fn(Product $record) => $record->category->icon)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->description(fn(Product $record) => $record->description)
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('size')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->badge()
                    ->icon("heroicon-o-cube")
                    ->color(fn(Product $record) => $record->stock > 0 ? 'success' : 'danger')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->description(fn(Product $record) => '('.$record->price . '+' . $record->vat . ' VAT' . ') - ' . $record->discount . ' Discount')
                    ->money('eur')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->placeholder('All Categories'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProducts::route('/'),
        ];
    }
}
