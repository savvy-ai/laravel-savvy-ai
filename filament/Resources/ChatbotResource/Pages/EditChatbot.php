<?php

namespace App\Filament\Resources\ChatbotResource\Pages;

use App\Filament\Resources\ChatbotResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChatbot extends EditRecord
{
    protected static string $resource = ChatbotResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function hasCombinedRelationManagerTabsWithForm(): bool
    {
        return false;
    }
}
