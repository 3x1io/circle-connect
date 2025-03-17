<?php

namespace App\Filament\Pages;

use App\Enums\AccountStatus;
use App\Filament\Pages\Traits\CanLoadAccount;
use App\Filament\Pages\Traits\HasAccountForm;
use App\Filament\Pages\Traits\HasEchoEvents;
use App\Filament\Pages\Traits\HasHeaderActions;
use App\Filament\Pages\Traits\HasKanbanBoard;
use App\Filament\Pages\Traits\HasSearchForm;
use App\Models\Account;
use App\Models\Order;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\On;
use Mokhosh\FilamentKanban\Concerns\HasEditRecordModal;
use Mokhosh\FilamentKanban\Concerns\HasStatusChange;
use TomatoPHP\FilamentDocs\Models\Document;
use TomatoPHP\FilamentDocs\Models\DocumentTemplate;
use UnitEnum;

class Dashboard extends \Filament\Pages\Dashboard implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;
    use HasEditRecordModal;
    use HasStatusChange;
    use HasSearchForm;
    use HasKanbanBoard;
    use HasAccountForm;
    use HasEchoEvents;
    use CanLoadAccount;
    use HasHeaderActions;

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
