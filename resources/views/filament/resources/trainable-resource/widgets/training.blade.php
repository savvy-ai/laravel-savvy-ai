<x-filament::widget>
    <x-filament::card>
        <x-filament::card.heading>
            Training
        </x-filament::card.heading>

        Upload a body of text to build your knowledge-base.

        <div class="mt-4">
            <x-filament::link href="{{ $trainingUrl }}">
                Begin training
                <x-heroicon-o-arrow-right class="w-4 h-4 ml-2" />
            </x-filament::link>
        </div>
    </x-filament::card>
</x-filament::widget>
