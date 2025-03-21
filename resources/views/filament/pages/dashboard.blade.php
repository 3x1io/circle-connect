@php use TomatoPHP\FilamentDocs\Models\DocumentTemplate; @endphp
<x-filament-panels::page>
    @if($this->getAccount)
        @php
            $docs = DocumentTemplate::query()->where('is_active', 1)->get();
        @endphp

       <div class="flex flex-wrap gap-4">
           @foreach($docs as $doc)
               <button x-tooltip="{
                    content: '{{ $doc->name }}',
                    theme: $store.theme
               }" wire:click="firePrintDocument({{ $doc->id }})" class="p-3 rounded-lg shadow-md flex flex-wrap justify-start gap-2" style="background-color: {{ $doc->color }};">
                   <x-icon name="{{ $doc->icon }}" class="h-4 w-4 text-white" />
               </button>
           @endforeach
       </div>

        <form action="{{ url()->current() }}" wire:submit="save">
            {{ $this->accountForm }}
        </form>

        <x-filament-widgets::widgets :widgets="[
            \App\Filament\Widgets\ActionTableWidget::class,
            \App\Filament\Widgets\DocumentTableWidget::class,
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
    @endif

    <x-filament-actions::modals />
</x-filament-panels::page>
