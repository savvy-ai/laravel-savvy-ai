<?php

namespace SavvyAI\Console\Commands;

use SavvyAI\Models\Statement;
use SavvyAI\Savvy\Vectorizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SavvyPineconeAdd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savvy:add {statement}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add vectorized statement to pinecone';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userId = 1;

        $statement = new Statement([
            'user_id'   => $userId,
            'statement' => $this->argument('statement'),
            'category'  => 'tuning',
        ]);

        if (!$statement->save())
        {
            $this->error('Unable to save record');
        }

        (new Vectorizer())->vectorize([$statement], sprintf('user-%d', $userId));

        return Command::SUCCESS;
    }
}
