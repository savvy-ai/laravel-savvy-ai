<?php

namespace App\Filament\Resources\ChatResource\Pages;

use App\Filament\Resources\ChatResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewChat extends ViewRecord
{
    protected static string $resource = ChatResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make()
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ChatResource\Widgets\ChatConversation::class
        ];
    }

    protected function getRelationManagers(): array
    {
        return [];
    }
}
