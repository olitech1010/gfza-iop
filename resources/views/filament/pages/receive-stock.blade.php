<x-filament-panels::page>
    <form wire:submit="receive">
        {{ $this->form }}

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ url('/admin/stores-overview') }}"
               class="fi-btn fi-btn-size-md relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-gray fi-btn-color-gray fi-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-white text-gray-950 hover:bg-gray-50 dark:bg-white/5 dark:text-white dark:hover:bg-white/10 ring-1 ring-gray-950/10 dark:ring-white/20">
                Cancel
            </a>
            <x-filament::button type="submit" color="success" icon="heroicon-o-arrow-down-tray">
                Receive All Items
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
