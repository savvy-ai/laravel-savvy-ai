<x-filament::page>
<form wire:submit.prevent="submit">
    {{ $this->form }}

    <br/>

    <div class="text-right mt-8" wire:loading.remove wire:target="submit">
        <x-filament::button type="submit">
            Start Training
        </x-filament::button>
    </div>

    <div class="text-right mt-8" wire:loading.block wire:target="submit">
        <x-filament::button disabled>
            <svg class="inline animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Training...
        </x-filament::button>
    </div>
</form>
</x-filament::page>
