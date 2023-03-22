<?php

namespace SavvyAI\Features\Chatting\Tools;

use SavvyAI\Models\Chat;
use SavvyAI\Models\Message;
use SavvyAI\Models\Statement;
use SavvyAI\Features\Chatting\Role;
use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;

class GetPropertyStatements
{
    public function use(Chat $chat, Message $incomingMessage): Message
    {
        $user     = $chat->user;
        $property = $chat->property;

        $embeddings = OpenAI::embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $incomingMessage->content,
        ]);

        $matches = Http::pinecone()->post('/query', [
            'vector'    => $embeddings->embeddings[0]->embedding,
            'namespace' => $user->uuid,
            'topK'      => 5,
            'filter'    => ['property_id' => $property->id]
        ])->json('matches');

        $statementIds = collect($matches)->pluck('id')->toArray();
        $statements   = Statement::whereIn('id', $statementIds)->get();

        if ($statements->isEmpty())
        {
            $statements = Statement::where('id', '>', 3)->get();
        }

        $outgoingMessage = new Message([
            'role' => Role::Assistant->value,
            'content' => implode(
                PHP_EOL,
                $statements->map(fn ($statement) => $statement->statement)->toArray()
            ),
        ]);

        $outgoingMessage->formattedAsReply = false;

        return $outgoingMessage;
    }
}
