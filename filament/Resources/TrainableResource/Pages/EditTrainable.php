<?php

namespace App\Filament\Resources\TrainableResource\Pages;

use App\Filament\Resources\TrainableResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrainable extends EditRecord
{
    protected static string $resource = TrainableResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRelationManagers(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.resources.trainables.view', $this->record->id);
    }
}
