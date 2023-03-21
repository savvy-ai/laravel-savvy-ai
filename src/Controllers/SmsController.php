<?php

namespace SavvyAI\Http\Controllers;

use App\Models\Chat\Chat;
use App\Models\Chat\Message;
use App\Models\Property;
use App\Savvy\Facades\Savvy;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Twilio\TwiML\MessagingResponse;

class SmsController extends Controller
{
    protected $client;

    public function __construct()
    {
//        $this->client = new Client(config('services.twilio.sid'), config('services.twilio.token'));
    }

    public function chat(Request $request): string
    {
        $request->validate(['prompt' => 'required|string|max:255', 'phone' => 'required|string|min:7']);

        try
        {
            $reply = Savvy::delegate($request->input('prompt'), $property->user, $property)->text() ?? '';

            $response = new MessagingResponse();
            $response->message($reply);

            $chat = Chat::query()
                ->where('name', $request->input('phone'))
                ->firstOrCreate(['name' => $request->input('phone')]);

            Message::query()->updateOrCreate([
                'user_id'         => $property->user->id,
                'property_id'     => $property->id,
                'chat_id' => $chat->id,
                'prompt'          => $request->input('prompt'),
                'completion'      => $reply,
            ]);

            $this->sendMessage($reply ?? 'Sorry, I did not understand that.', $request->input('phone'));

            return $reply;
        }
        catch (\Exception $e)
        {
            Log::error($e->getMessage());

            return json_encode([
                'input'  => $request->prompt,
                'answer' => 'Sorry it seems our service is having a hiccup, please try again.',
            ], true);
        }
    }

    public function webhook(Request $request): string
    {
        Log::debug('SMS Webhook', $request->all());

        $request->validate(['To' => 'required', 'From' => 'required', 'Body' => 'required']);

        $property = Property::where('phone', $request->input('To'))->first();

        if (! $property)
        {
            $response = new MessagingResponse();
            $response->message('Sorry, we don\'t have a property for this number.');

            return $response->asXML();
        }

        try
        {
            $body = $request->input('Body', 'User did not type anything but likely sent a recording.');

            $reply = Savvy::delegate($body, $property->user, $property)->text() ?? '';

            $response = new MessagingResponse();
            $response->message($reply);

            $chat = Chat::query()
                ->where('name', $request->input('From'))
                ->firstOrCreate(['name' => $request->input('From')]);

            Message::query()->updateOrCreate([
                'user_id'         => $property->user->id,
                'property_id'     => $property->id,
                'chat_id' => $chat->id,
                'prompt'          => $body,
                'completion'      => $reply,
            ]);

            return $response->asXML();
        }
        catch (\Exception $e)
        {
            Log::error($e->getMessage());

            $response = new MessagingResponse();
            $response->message('Sorry it seems our service is having a hiccup, please try again.');

            return $response->asXML();
        }
    }

    private function sendMessage(string $body, string $recipient, string $sender = null): \Twilio\Rest\Api\V2010\Account\MessageInstance
    {
        $sender = $sender ?? config('services.twilio.phone');

        return $this->client->messages->create(
            $recipient,
            [
                'from' => $sender,
                'body' => $body,
            ]
        );
    }
}
