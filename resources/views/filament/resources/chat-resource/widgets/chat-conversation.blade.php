<x-filament::widget>
    <x-filament::card>
        <div class="h-[500px] overflow-y-auto scrolling-touch scroll-smooth p-4">
            <div class="flex flex-col mt-auto space-y-8">
                @foreach($messages as $message)
                    <div class="chat-message">
                        @if($message->role === 'user')
                            <div class="flex items-end justify-end">
                                <div class="flex flex-col space-y-2 max-w-md items-start">
                                    <div class="px-4 py-2 rounded-lg inline-block font-semibold bg-primary-600 text-white">
                                        {{ $message->content }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($message->role === 'assistant')
                            <div class="flex items-end">
                                <div class="flex flex-col space-y-2 max-w-md items-end">
                                    <div class="inline-block rounded-lg font-semibold bg-gray-100">
                                        <div class="px-4 py-2">
                                            {{ $message->content }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
