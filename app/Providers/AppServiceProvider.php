<?php

namespace App\Providers;

use App\Filament\Widgets\ActionTableWidget;
use App\Filament\Widgets\DocumentTableWidget;
use App\Filament\Widgets\OrderTableWidget;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use TomatoPHP\FilamentDocs\Facades\FilamentDocs;
use TomatoPHP\FilamentDocs\Services\Contracts\DocsVar;
use TomatoPHP\FilamentTypes\Facades\FilamentTypes;
use TomatoPHP\FilamentTypes\Services\Contracts\Type;
use TomatoPHP\FilamentTypes\Services\Contracts\TypeFor;
use TomatoPHP\FilamentTypes\Services\Contracts\TypeOf;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::component('action.table', ActionTableWidget::class);
        Livewire::component('order.table', OrderTableWidget::class);
        Livewire::component('document.table', DocumentTableWidget::class);

        FilamentTypes::register([
            TypeFor::make('accounts')
                ->label('Accounts')
                ->types([
                    TypeOf::make('type')
                        ->label('Type')
                        ->register([
                            Type::make('lead')
                                ->name('Lead')
                                ->icon('heroicon-o-user-group')
                                ->color('#09d414')
                        ])
                ])
        ]);
        FilamentDocs::register([
            DocsVar::make('$ADDRESS')
                ->label('Customer Address'),
            DocsVar::make('$NAME')
                ->label('Customer Full Name'),
            DocsVar::make('$FAX')
                ->label('Customer Fax Number'),
        ]);
    }
}
