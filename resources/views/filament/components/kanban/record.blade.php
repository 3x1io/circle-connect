<div
    id="{{ $record->getKey() }}"
    wire:click="recordClicked('{{ $record->getKey() }}', {{ @json_encode($record) }})"
    class="record bg-white dark:bg-gray-700 rounded-lg px-4 py-2 cursor-grab font-medium text-gray-600 dark:text-gray-200"
    @if($record->timestamps && now()->diffInSeconds($record->{$record::UPDATED_AT}, true) < 3)
        x-data
    x-init="
            $el.classList.add('animate-pulse-twice', 'bg-primary-100', 'dark:bg-primary-800')
            $el.classList.remove('bg-white', 'dark:bg-gray-700')
            setTimeout(() => {
                $el.classList.remove('bg-primary-100', 'dark:bg-primary-800')
                $el.classList.add('bg-white', 'dark:bg-gray-700')
            }, 3000)
        "
    @endif
>
    <div class="flex justify-start gap-4">
        <div class="flex flex-col justify-center items-center">
            <x-filament::avatar size="lg" src="{{ $record->getFilamentAvatarUrl()?: 'https://ui-avatars.com/api/?name='.$record->name.'&color=FFFFFF&background=020617' }}" />
        </div>
        <div class="flex flex-col justify-start gap-1">
            <div class="font-bold text-md">
                {{ $record->{static::$recordTitleAttribute} }}
            </div>
            <div class="text-sm">
                {{ $record->phone }}
            </div>
        </div>
    </div>
</div>
