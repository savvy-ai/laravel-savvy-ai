<x-filament::widget>
    <x-filament::card>
        <x-filament::card.heading>
            Chat
        </x-filament::card.heading>

        The URL of the {{ $record->name }} chat.

        <div class="mt-4">
            <x-filament::link href="{{ $chatLink }}" target="_blank">
                Go to chat
                <x-heroicon-o-arrow-right class="w-4 h-4 ml-2" />
            </x-filament::link>
        </div>
    </x-filament::card>
</x-filament::widget>
