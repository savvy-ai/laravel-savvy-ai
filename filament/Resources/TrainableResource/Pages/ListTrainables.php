<?php

namespace App\Filament\Resources\TrainableResource\Pages;

use App\Filament\Resources\TrainableResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrainables extends ListRecords
{
    protected static string $resource = TrainableResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
