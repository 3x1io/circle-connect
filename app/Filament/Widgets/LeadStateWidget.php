<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use TomatoPHP\FilamentDocs\Models\Document;

class LeadStateWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Leads', Account::query()->where('type', 'lead')->count())
                ->description('Total Leads')
                ->chart([50, 20, 30, 40, 20, 50, 30, 40, 20, 50])
                ->color('warning')
                ->icon('heroicon-s-users'),
            Stat::make('Customers', Account::query()->where('type', 'customer')->count())
                ->description('Total Customers')
                ->chart([50, 20, 30, 40, 20, 50, 30, 40, 20, 50])
                ->color('success')
                ->icon('heroicon-s-users'),
            Stat::make('Documents', Document::query()->count())
                ->description('Total Documents')
                ->chart([50, 20, 30, 40, 20, 50, 30, 40, 20, 50])
                ->color('info')
                ->icon('heroicon-s-document'),
        ];
    }
}
