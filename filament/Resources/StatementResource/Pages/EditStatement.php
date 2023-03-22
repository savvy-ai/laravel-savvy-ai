<?php

namespace App\Filament\Resources\StatementResource\Pages;

use App\Filament\Resources\StatementResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStatement extends EditRecord
{
    protected static string $resource = StatementResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
