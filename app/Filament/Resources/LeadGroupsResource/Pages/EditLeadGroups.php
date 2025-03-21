<?php

namespace App\Filament\Resources\LeadGroupsResource\Pages;

use App\Filament\Resources\LeadGroupsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeadGroups extends EditRecord
{
    protected static string $resource = LeadGroupsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
