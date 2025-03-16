<x-filament-panels::page>
    @if($this->getAccount)

        <form action="{{ url()->current() }}" wire:submit="save">
            {{ $this->form }}

            <div class="mt-4">
                {{ $this->saveAction() }}
            </div>
        </form>

        <x-filament-widgets::widgets :widgets="[
            \App\Filament\Widgets\DocumentTableWidget::class,
            \App\Filament\Widgets\ActionTableWidget::class,
            \App\Filament\Widgets\OrderTableWidget::class,
        ]" />
    @else
        <form action="{{ url()->current() }}" wire:submit="searchAction">
            {{ $this->accountSearchForm }}

            <div class="mt-4">
                {{ $this->accountSearchAction() }}
            </div>
        </form>
    @endif

    <x-filament-actions::modals />



</x-filament-panels::page>
