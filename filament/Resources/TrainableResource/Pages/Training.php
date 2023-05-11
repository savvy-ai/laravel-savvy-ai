<?php

namespace App\Filament\Resources\TrainableResource\Pages;

use App\Filament\Resources\TrainableResource;
use App\Jobs\InitialTrainingJob;
use App\Savvy\Config\TrainingConfig;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\Concerns\HasRecordBreadcrumb;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Training extends Page
{
    use HasRecordBreadcrumb;
    use InteractsWithRecord;

    public string $data;
    public bool   $isTraining   = false;
    public bool   $isProcessing = false;
    public bool   $isGenerating = false;
    public bool   $isComplete   = false;

    protected static string $resource = TrainableResource::class;

    protected static string $view = 'filament.resources.trainable-resource.pages.training';

    public function mount($record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getHeading(): string
    {
        return "Bulk Training for {$this->record->name}";
    }

    public function getListeners(): array
    {
        return [
            "echo-private:App.Properties.{$this->record->id},TrainingEvent" => 'notifyTrainingEvent',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Textarea::make('data')
                ->placeholder('Paste your property data/manual/rules here.')
                ->rows(15)
        ];
    }

    public function submit()
    {
        $this->validate([
            'data' => 'required',
        ]);

        DB::beginTransaction();

        try
        {
            $this->isTraining = true;

            $this->record->update([
                'is_training' => true,
            ]);

            $trainingConfig = new TrainingConfig([
                'maxSegmentTokens' => 250,
                'user'             => auth()->user(),
                'property'         => $this->record,
                'metadata'         => [
                    'property_id' => $this->record->id,
                ],
            ]);

            InitialTrainingJob::dispatch($this->data, $trainingConfig)->afterCommit();

            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollBack();

            Log::error($e->getMessage());

            return redirect()->back()->withErrors([
                'text' => $e->getMessage(),
            ]);
        }

        return redirect()->back();
    }

    public function notifyTrainingEvent($payload)
    {
        $name = $payload['eventName'];

        if ($name === TrainingConfig::SEGMENTING)
        {
            $this->isProcessing = true;
        }

        if ($name === TrainingConfig::SUMMARIZING)
        {
            $this->isGenerating = true;
        }

        if ($name === TrainingConfig::COMPLETED)
        {
            $this->isComplete = true;

            $this->record->update([
                'is_training' => false,
            ]);
        }
    }
}
