<x-filament-panels::page class="fi-dashboard-page">
    @isset($this->filtersForm)
        {{ $this->filtersForm }}
    @endisset

    <x-filament-widgets::widgets :columns="$this->getColumns()" :data="array_merge(
        property_exists($this, 'filters') ? ['filters' => $this->filters] : [],
        $this->getWidgetData()
    )" :widgets="$this->getVisibleWidgets()" />
</x-filament-panels::page>