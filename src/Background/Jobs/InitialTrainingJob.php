<?php

namespace SavvyAI\Jobs;

use SavvyAI\Config\TrainingConfig;
use SavvyAI\SavvyAI;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class InitialTrainingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $text;
    protected TrainingConfig $config;
    public int $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $text, TrainingConfig $config)
    {
        $this->text   = $text;
        $this->config = $config;

        $this->onQueue('training');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $savvy = new SavvyAI();

        $savvy->train($this->text, $this->config);
    }

    public function failed(Throwable $exception): void
    {
        $this->config->property->update([
            'is_training' => false,
        ]);
    }
}
