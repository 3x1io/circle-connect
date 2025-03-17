<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Traits\CanLoadAccount;
use App\Filament\Pages\Traits\HasAccountForm;
use App\Filament\Pages\Traits\HasEchoEvents;
use App\Filament\Pages\Traits\HasHeaderActions;
use App\Filament\Pages\Traits\HasKanbanBoard;
use App\Filament\Pages\Traits\HasSearchForm;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Mokhosh\FilamentKanban\Concerns\HasEditRecordModal;
use Mokhosh\FilamentKanban\Concerns\HasStatusChange;

class Dashboard extends \Filament\Pages\Dashboard implements HasActions, HasForms
{
    use CanLoadAccount;
    use HasAccountForm;
    use HasEchoEvents;
    use HasEditRecordModal;
    use HasHeaderActions;
    use HasKanbanBoard;
    use HasSearchForm;
    use HasStatusChange;
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $view = 'filament.pages.dashboard';

    public function getTitle(): string | Htmlable
    {
        return $this->getAccount ? (new HtmlString($this->getAccount->name . ' <div class="text-sm font-bold" >+' . $this->getAccount->phone . '</div>')) : 'Dashboard';
    }

    protected function getForms(): array
    {
        return [
            'accountForm', 'accountSearchForm', 'form',
        ];
    }

    public function notify(): void
    {
        Notification::make()
            ->title('Success')
            ->body('Data saved successfully!')
            ->success()
            ->send();
    }
}
