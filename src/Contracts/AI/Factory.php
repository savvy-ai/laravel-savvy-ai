<?php

namespace SavvyAI\Contracts\AI;

interface Factory
{
    public function createClient(string $name) : Client;
}
