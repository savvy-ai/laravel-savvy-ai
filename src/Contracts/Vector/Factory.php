<?php

namespace SavvyAI\Contracts\Vector;

interface Factory
{
    public function createClient(string $name) : Client;
}
