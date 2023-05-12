<?php

namespace App\Filament\Resources\TrainableResource\Pages;

use App\Filament\Resources\TrainableResource;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\Concerns\HasRecordBreadcrumb;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SavvyAI\Models\Trainable;
use SavvyAI\Savvy;

/**
 * @property Trainable $record
 */
class Training extends Page
{
    use HasRecordBreadcrumb;
    use InteractsWithRecord;

    public string $text         = '';
    public string $delimiter    = '';
    public array  $upload       = [];
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
        return sprintf('Training for %s', $this->record->name);
    }

    protected function getFormSchema(): array
    {
        return [
            Tabs::make('Heading')
                ->tabs([
                    Tab::make('Text')
                        ->schema([
                            TextArea::make('delimiter')
                                ->label('Delimiter')
                                ->rows(2),
                            Textarea::make('text')
                                ->label('Text')
                                ->rows(15)
                        ]),
                    Tab::make('Upload')
                        ->schema([
                            TextArea::make('delimiter')
                                ->label('Delimiter')
                                ->rows(2),
                            FileUpload::make('upload')
                                ->label('Upload')
                                ->acceptedFileTypes(['text/plain'])
                                ->disk('local')
                                ->preserveFilenames()
                        ]),
                ]),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'backUrl' => route('filament.resources.trainables.view', $this->record)
        ];
    }

    public function submit()
    {
        $this->validate([
            'text'   => 'required_without:upload',
            'upload' => 'required_without:text',
        ]);

        DB::beginTransaction();

        try
        {
            $this->isTraining = true;

            $this->record->update([
                'is_training' => true,
            ]);

            $this->record->splitAt = $this->delimiter;

            if ($this->upload)
            {
                $upload   = array_shift($this->upload);
                $contents = file_get_contents($upload->getRealPath());

                if (!is_string($contents))
                {
                    throw new \Exception('File contents could not be read');
                }

                $this->text = $contents;
            }

            Savvy::train($this->record, $this->text, $this->record->id);

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

        $this->record->update([
            'is_training' => false,
        ]);

        return redirect()->back();
    }
}
