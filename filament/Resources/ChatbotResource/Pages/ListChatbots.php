<?php

namespace App\Filament\Resources\ChatbotResource\Pages;

use App\Filament\Resources\ChatbotResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChatbots extends ListRecords
{
    protected static string $resource = ChatbotResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
