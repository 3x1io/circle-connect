<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentTemplateResource\Pages;
use TomatoPHP\FilamentDocs\Models\DocumentTemplate;

class DocumentTemplateResource extends \TomatoPHP\FilamentDocs\Filament\Resources\DocumentTemplateResource
{
    protected static ?string $model = DocumentTemplate::class;

    public static function getPluralLabel(): ?string
    {
        return 'Documents';
    }

    public static function getLabel(): ?string
    {
        return 'Document';
    }

    public static function getNavigationLabel(): string
    {
        return 'Documents';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDocumentTemplates::route('/'),
        ];
    }
}
