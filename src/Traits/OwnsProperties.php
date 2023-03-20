<?php

namespace SavvyAI\Traits;

use SavvyAI\Models\Property;

trait OwnsProperties
{
    /**
     * Determine whether the user owns the given property.
     */
    public function owns(Property $property): bool
    {
        return $this->id === $property->user_id;
    }

    public function canOwnMoreProperties(): bool
    {
        $propertiesOwned = $this->properties()->count();

        foreach (config('savvy.checkout.plans') as $plan)
        {
            if ($this->subscribed($plan['name']))
            {
                return $propertiesOwned < $plan['property_limit'];
            }
        }

        return false;
    }
}
