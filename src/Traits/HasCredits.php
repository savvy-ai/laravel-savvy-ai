<?php

namespace SavvyAI\Traits;

trait HasCredits
{
    public function setCredits(float $credits): self
    {
        $this->credits = $credits;

        return $this;
    }

    public function addCredits(float $credits): self
    {
        $this->credits += $credits;

        return $this;
    }

    public function subtractCredits(float $credits): self
    {
        $this->credits -= $credits;

        return $this;
    }

    public function hasCredits(): bool
    {
        return $this->credits > 0;
    }

    public function hasEnoughCredits(float $credits): bool
    {
        return $this->credits > $credits;
    }

    public function tokensToCredits(int $tokens): float
    {
        return $tokens / $this->tokensPerCredit();
    }

    public function tokensPerCredit(): int
    {
        if ($this->subscribed('standard'))
        {
            return config('savvy.checkout.plans.standard.tokens_per_credit') ?? 500;
        }

        if ($this->subscribed('professional'))
        {
            return config('savvy.checkout.plans.professional.tokens_per_credit') ?? 400;
        }

        return 100;
    }

    public function creditsPerPlan(string $plan): int
    {
        return config(sprintf('savvy.checkout.plans.%s.tokens_per_credit', $plan)) ?? 0;
    }
}
