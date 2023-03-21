<?php

namespace SavvyAI\Listeners;

use SavvyAI\Events\TrainingEvent;
use SavvyAI\Models\Property;
use SavvyAI\Config\TrainingConfig;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TrainingListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param \SavvyAI\Events\TrainingEvent $event
     * @return void
     */
    public function handle(TrainingEvent $event)
    {
        if ($event->eventName === TrainingConfig::COMPLETED)
        {
            $property = Property::query()->findOrFail($event->propertyId);

            $property->update([
                'is_training' => false,
            ]);
        }
    }
}
