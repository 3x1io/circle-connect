<?php

namespace App\Filament\Resources\LeadGroupsResource\Pages;

use App\Filament\Resources\LeadGroupsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ManageRecords;

class ListLeadGroups extends ManageRecords
{
    protected static string $resource = LeadGroupsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
