<?php

namespace App\Filament\Pages\Traits;

use App\Enums\AccountStatus;
use App\Filament\Pages\Dashboard;
use App\Models\Account;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait HasKanbanBoard
{
    protected static string $model = Account::class;
    protected static string $statusEnum = AccountStatus::class;

    protected static string $headerView = 'filament.components.kanban.header';

    protected static string $recordView = 'filament.components.kanban.record';

    protected static string $statusView = 'filament.components.kanban.status';

    protected static string $scriptsView = 'filament.components.kanban.scripts';

    protected static string $recordTitleAttribute = 'name';

    protected static string $recordStatusAttribute = 'type';


    public ?array $editModalFormState = [];

    public null | int | string $editModalRecordId = null;

    protected string $editModalTitle = 'Edit Record';

    protected bool $editModalSlideOver = false;

    protected string $editModalWidth = '2xl';

    protected string $editModalSaveButtonLabel = 'Save';

    protected string $editModalCancelButtonLabel = 'Cancel';


    public function recordClicked(int | string $recordId, array $data): void
    {

        $account = Account::query()->find($recordId);
        if($account){
            $this->redirect(self::getUrl().'?search=' . $account->phone);
        }
    }

    public function editModalFormSubmitted(): void
    {
        $this->editRecord($this->editModalRecordId, $this->form->getState(), $this->editModalFormState);

        $this->editModalRecordId = null;
        $this->form->fill();

        $this->dispatch('close-modal', id: 'kanban--edit-record-modal');
    }


    protected function getEditModalRecordData(int | string $recordId, array $data): array
    {
        return $this->getEloquentQuery()->find($recordId)->toArray();
    }

    protected function editRecord(int | string $recordId, array $data, array $state): void
    {
        $this->getEloquentQuery()->find($recordId)->update($data);
    }

    protected function getEditModalFormSchema(null | int | string $recordId): array
    {
        return [
            TextInput::make(static::$recordTitleAttribute),
        ];
    }

    protected function getEditModalTitle(): string
    {
        return $this->editModalTitle;
    }

    protected function getEditModalSlideOver(): bool
    {
        return $this->editModalSlideOver;
    }

    protected function getEditModalWidth(): string
    {
        return $this->editModalWidth;
    }

    protected function getEditModalSaveButtonLabel(): string
    {
        return $this->editModalSaveButtonLabel;
    }

    protected function getEditModalCancelButtonLabel(): string
    {
        return $this->editModalCancelButtonLabel;
    }

    protected function statuses(): Collection
    {
        return collect([
            [
                'id' => 'lead',
                'title' => 'Lead'
            ],
            [
                'id' => 'customer',
                'title' => 'Customer'
            ],
            [
                'id' => 'inactive',
                'title' => 'Inactive'
            ],
        ]);
    }

    protected function records(): Collection
    {
        return $this->getEloquentQuery()
            ->when(method_exists(static::$model, 'scopeOrdered'), fn ($query) => $query->ordered())
            ->get();
    }

    protected function getViewData(): array
    {
        $records = $this->records();
        $statuses = $this->statuses()
            ->map(function ($status) use ($records) {
                $status['records'] = $this->filterRecordsByStatus($records, $status);

                return $status;
            });

        return [
            'statuses' => $statuses,
        ];
    }

    protected function filterRecordsByStatus(Collection $records, array $status): array
    {
        $statusIsCastToEnum = $records->first()?->getAttribute(static::$recordStatusAttribute) instanceof UnitEnum;

        $filter = $statusIsCastToEnum
            ? static::$statusEnum::from($status['id'])
            : $status['id'];

        return $records->where(static::$recordStatusAttribute, $filter)->all();
    }

    protected function getEloquentQuery(): Builder
    {
        return static::$model::query();
    }


}
