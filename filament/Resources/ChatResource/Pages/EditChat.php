<?php

namespace App\Filament\Resources\ChatResource\Pages;

use App\Filament\Resources\ChatResource;
use Filament\Forms\Components\Select;
use Filament\Pages\Actions;
use Filament\Resources\Form;
use Filament\Resources\Pages\EditRecord;

class EditChat extends EditRecord
{
    protected static string $resource = ChatResource::class;

    protected function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('trainable')
                    ->relationship('trainable', 'name')
                    ->columnSpanFull(),
                Select::make('agent')
                    ->label('Current Agent')
                    ->relationship('agent', 'name'),
                Select::make('dialogue')
                    ->label('Current Dialogue')
                    ->relationship('dialogue', 'name'),
            ]);
    }

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
