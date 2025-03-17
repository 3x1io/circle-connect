<?php

namespace App\Filament\Pages\Traits;

use App\Enums\AccountStatus;
use App\Models\Account;
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

    protected function statuses(): Collection
    {
        return collect([
            [
                'id' => 'lead',
                'title' => 'Lead',
            ],
            [
                'id' => 'customer',
                'title' => 'Customer',
            ],
            [
                'id' => 'inactive',
                'title' => 'Inactive',
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
