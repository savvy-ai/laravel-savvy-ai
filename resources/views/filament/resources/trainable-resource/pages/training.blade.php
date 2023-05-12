<x-filament::page>
    @if($isTraining)
        <div class="training w-full py-6">

            <div class="flex flex-col gap-8">

                {{-- processing --}}
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div @class(['!bg-primary-500 !text-white' => $isProcessing, 'circle w-10 h-10 mx-auto bg-white border-2 border-slate-200 rounded-full text-lg text-slate-600 flex items-center'])>
                            <div class="text-center w-full">
                                <svg class="w-full fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                    <path class="heroicon-ui" d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-2.3-8.7l1.3 1.29 3.3-3.3a1 1 0 0 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-2-2a1 1 0 0 1 1.4-1.42z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="text-lg">Processing Text</div>
                </div>

                {{-- generating --}}
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div @class(['!bg-primary-500 !text-white' => $isGenerating, 'circle w-10 h-10 mx-auto bg-white border-2 border-slate-200 rounded-full text-lg text-slate-600 flex items-center'])>
                            <div class="text-center w-full">
                                <svg class="w-full fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                    <path class="heroicon-ui" d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-2.3-8.7l1.3 1.29 3.3-3.3a1 1 0 0 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-2-2a1 1 0 0 1 1.4-1.42z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="text-lg">Generating Vocabulary</div>
                </div>

                {{-- complete --}}
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div @class(['!bg-primary-500 !text-white' => $isComplete, 'circle w-10 h-10 mx-auto bg-white border-2 border-slate-200 rounded-full text-lg text-slate-600 flex items-center'])>
                            <div class="text-center w-full">
                                <svg class="w-full fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                    <path class="heroicon-ui" d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-2.3-8.7l1.3 1.29 3.3-3.3a1 1 0 0 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-2-2a1 1 0 0 1 1.4-1.42z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="text-lg">Training Complete</div>
                </div>
            </div>
        </div>

        @if($isComplete)
            <div class="text-center mt-8">
                <x-filament::button tag="a" href="{{ $backUrl }}">
                    Go to {{ $record->name }}
                </x-filament::button>
            </div>
        @endif
    @else
        <form wire:submit.prevent="submit">
            {{ $this->form }}

            <div class="text-right mt-8">
                <x-filament::button type="submit">
                    Start Training
                </x-filament::button>
            </div>
        </form>
    @endif
</x-filament::page>
