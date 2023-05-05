<?php

namespace App\Filament\Resources\TrainableResource\Widgets;

use Filament\Widgets\Widget;
use SavvyAI\Models\Trainable;

class Training extends Widget
{
    public Trainable $record;

    protected static string $view = 'filament.resources.trainable-resource.widgets.training';

    protected function getViewData(): array
    {
        return [
            'trainingUrl' => route('filament.resources.domains.training', $this->record),
        ];
    }
}
