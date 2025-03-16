<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use TomatoPHP\FilamentDocs\Filament\Resources\DocumentResource;
use TomatoPHP\FilamentDocs\Filament\Resources\DocumentTemplateResource;
use TomatoPHP\FilamentDocs\Models\Document;

class DocumentTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Docs';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return DocumentResource::table($table)
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->prefix('#')
                    ->label(trans('filament-docs::messages.documents.form.id'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('documentTemplate.name')
                    ->badge()
                    ->color('warning')
                    ->icon(fn ($record) => $record->documentTemplate->icon)
                    ->label('Document')
                    ->url(fn ($record) => DocumentTemplateResource::getUrl('edit', ['record' => $record->documentTemplate->id]))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->description(fn ($record) => $record->created_at->diffForHumans())
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->query(
                Document::query()
                    ->where('model_id', session('model_id'))
                    ->where('model_type', session('model_type'))
            );
    }
}
