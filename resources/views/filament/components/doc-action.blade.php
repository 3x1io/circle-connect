<x-filament-actions::action
    :action="$action"
    :badge="$getBadge()"
    :badge-color="$getBadgeColor()"
    dynamic-component="filament::button"
    :icon-position="$getIconPosition()"
    :labeled-from="$getLabeledFromBreakpoint()"
    :outlined="$isOutlined()"
    :size="$getSize()"
    id="icon-color-{{ $action->getName() }}"
    class="fi-ac-btn-action"
>
    {{ $getLabel() }}
</x-filament-actions::action>
<style>
    #icon-color-{{ $action->getName() }} {
        background-color: {{ $getColor() }} !important;
    }
</style>
