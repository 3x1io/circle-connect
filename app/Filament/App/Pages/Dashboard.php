<?php

namespace App\Filament\App\Pages;

use App\Filament\Pages\Traits\CanLoadAccount;
use App\Filament\Pages\Traits\HasAccountForm;
use App\Filament\Pages\Traits\HasEchoEvents;
use App\Filament\Pages\Traits\HasHeaderActions;
use App\Filament\Pages\Traits\HasKanbanBoard;
use App\Filament\Pages\Traits\HasSearchForm;
use App\Models\Account;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Mokhosh\FilamentKanban\Concerns\HasStatusChange;

class Dashboard extends \Filament\Pages\Dashboard implements HasActions, HasForms
{
    use CanLoadAccount;
    use HasAccountForm;
    use HasEchoEvents;
    use HasHeaderActions;
    use HasKanbanBoard;
    use HasSearchForm;
    use HasStatusChange;
    use InteractsWithActions;
    use InteractsWithForms;

    public function mount()
    {
        $this->form->fill();

        if (request()->has('search') && ! empty(request()->get('search'))) {
            $this->getAccount = Account::query()
                ->where('phone', 'LIKE', '%' . request()->get('search') . '%')
                ->whereHas('teams', function (Builder $query) {
                    $query->where('team_id', filament()->getTenant()->id);
                })
                ->orWhere('email', 'LIKE', '%' . request()->get('search') . '%')
                ->whereHas('teams', function (Builder $query) {
                    $query->where('team_id', filament()->getTenant()->id);
                })
                ->first();

            if ($this->getAccount) {
                $this->loadAccount();
            } else {
                $this->getAccount = Account::query()->create([
                    'phone' => request()->get('search'),
                    'username' => request()->get('search'),
                    'type' => 'lead',
                ]);

                session()->put('model_id', $this->getAccount->id);
                session()->put('model_type', get_class($this->getAccount));
            }
        }
    }

    protected function getEloquentQuery(): Builder
    {
        return static::$model::query()->whereHas('teams', function (Builder $query) {
            $query->where('team_id', filament()->getTenant()->id);
        });
    }

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
