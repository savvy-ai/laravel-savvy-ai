<?php

namespace App\Filament\Resources\TrainableResource\Pages;

use App\Filament\Resources\TrainableResource;
use Filament\Pages\Actions\Action;
use Filament\Resources\Form;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewTrainable extends ViewRecord
{
    /* @var \SavvyAI\Models\Trainable $resource */
    public $record;

    protected static string $resource = TrainableResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('Publish')
                ->icon('heroicon-o-lightning-bolt')
                ->color('secondary')
                ->action(fn () => $this->record->publish())
                ->hidden(function () {
                    return $this->record->published_at;
                }),
            Action::make('Unpublish')
                ->icon('heroicon-o-eye-off')
                ->color('secondary')
                ->action(fn () => $this->record->unpublish())
                ->hidden(function () {
                    return !$this->record->published_at;
                }),
            Action::make('Edit')
                ->icon('heroicon-o-pencil')
                ->url(route('filament.resources.trainables.edit', $this->record)),
        ];
    }

    protected function getHeading(): string|Htmlable
    {
        return $this->record->name;
    }

    public function form(Form $form): Form
    {
        return $form;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TrainableResource\Widgets\Training::class,
            TrainableResource\Widgets\ChatLink::class
        ];
    }
}
