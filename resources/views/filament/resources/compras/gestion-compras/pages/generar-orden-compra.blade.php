<x-filament-panels::page>
    {{ $this->form }}

    <div class="flex justify-end gap-x-3 mt-4"> 
        <x-filament::button wire:click="save">
            Generar Órdenes de Compra
        </x-filament::button>
    </div>
</x-filament-panels::page>

