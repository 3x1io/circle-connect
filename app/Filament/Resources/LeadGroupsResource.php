<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadGroupsResource\Pages;
use App\Filament\Resources\LeadGroupsResource\RelationManagers;
use App\Models\LeadGroups;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use TomatoPHP\FilamentAccounts\Components\AccountColumn;
use TomatoPHP\FilamentIcons\Components\IconColumn;
use TomatoPHP\FilamentIcons\Components\IconPicker;
use TomatoPHP\FilamentSaasPanel\Filament\Resources\TeamResource;

class LeadGroupsResource extends TeamResource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\SpatieMediaLibraryFileUpload::make('avatar')
                    ->label(trans('filament-accounts::messages.team.columns.avatar'))
                    ->hiddenLabel()
                    ->alignCenter()
                    ->columnSpanFull()
                    ->avatar()
                    ->collection('avatar')
                    ->image(),
                Forms\Components\TextInput::make('name'),
                Forms\Components\TextInput::make('description'),
                Forms\Components\ColorPicker::make('color'),
                IconPicker::make('icon')->label('icon'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('avatar')
                    ->collection('avatar')
                    ->circular()
                    ->label(trans('filament-accounts::messages.team.columns.avatar'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(trans('filament-accounts::messages.team.columns.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ColorColumn::make('color')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('icon')
                    ->searchable()
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
                Tables\Filters\SelectFilter::make('owner')
                    ->label(trans('filament-accounts::messages.team.columns.owner'))
                    ->searchable()
                    ->relationship('owner', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('id', 'desc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeadGroups::route('/')
        ];
    }
}
