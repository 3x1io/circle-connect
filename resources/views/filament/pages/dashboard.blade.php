<x-filament-panels::page>
    @if($this->getAccount)

        <form action="{{ url()->current() }}" wire:submit="save">
            {{ $this->accountForm }}
        </form>

        <x-filament-widgets::widgets :widgets="[
            \App\Filament\Widgets\DocumentTableWidget::class,
            \App\Filament\Widgets\ActionTableWidget::class,
            \App\Filament\Widgets\OrderTableWidget::class,
        ]" />
    @else
        <form action="{{ url()->current() }}" wire:submit="searchAction">
            {{ $this->accountSearchForm }}
        </form>

        <x-filament-widgets::widgets :widgets="[
            \App\Filament\Widgets\LeadStateWidget::class
        ]" />

        <div x-data wire:ignore.self class="md:flex overflow-x-auto overflow-y-hidden gap-4 pb-4">
            @foreach($statuses as $status)
                @include(static::$statusView)
            @endforeach

            <div wire:ignore>
                @include(static::$scriptsView)
            </div>
        </div>

        @unless($disableEditModal)
            <x-filament-kanban::edit-record-modal/>
        @endunless
    @endif

    <x-filament-actions::modals />



</x-filament-panels::page>
