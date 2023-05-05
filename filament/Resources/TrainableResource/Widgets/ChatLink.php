<?php

namespace App\Filament\Resources\TrainableResource\Widgets;

use Filament\Widgets\Widget;
use SavvyAI\Models\Trainable;

class ChatLink extends Widget
{
    public Trainable $record;

    protected static string $view = 'filament.resources.trainable-resource.widgets.chat-link';

    protected function getViewData(): array
    {
        return [
            'chatLink' => route('chat.ask', $this->record)
        ];
    }
}
