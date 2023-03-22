<?php

namespace App\Filament\Resources\TrainableResource\Pages;

use App\Filament\Resources\TrainableResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainable extends CreateRecord
{
    protected static string $resource = TrainableResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
