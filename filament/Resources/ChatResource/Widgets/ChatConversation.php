<?php

namespace App\Filament\Resources\ChatResource\Widgets;

use Filament\Widgets\Widget;
use SavvyAI\Models\Chat;

class ChatConversation extends Widget
{
    public Chat $record;

    protected static string $view = 'filament.resources.chat-resource.widgets.chat-conversation';

    protected int|string|array $columnSpan = 12;

    protected function getViewData(): array
    {
        return [
            'messages' => $this->record->messages()->orderBy('created_at', 'asc')->get()
        ];
    }
}
