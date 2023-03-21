<?php

namespace SavvyAI\Http\Controllers;

use App\Models\Chat\Chat;
use App\Models\Chat\Message;
use App\Models\Property;
use App\Savvy\Facades\Savvy;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Twilio\TwiML\MessagingResponse;

class WhatsAppController extends Controller
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
                'user_id'     => $property->user->id,
                'property_id' => $property->id,
                'chat_id'     => $chat->id,
                'prompt'      => $request->input('prompt'),
                'completion'  => $reply,
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
        Log::debug('WhatsApp Webhook', $request->all());

        $request->validate(['To' => 'required', 'From' => 'required', 'Body' => 'required']);

        $property = Property::query()->first();

        // $property = Property::where('phone', str_replace('whatsapp:', $request->input('To'), ''))->first();

        if (! $property)
        {
            $response = new MessagingResponse();
            $response->message('Sorry, we don\'t have a property for this number.');

            return $response->asXML();
        }

        try
        {
            // @todo: Add support for recording transcription by using:
            // - ReferralNumMedia = "0"
            // - MediaContentType0 = "audio/ogg"
            // - MediaUrl0 = "https://api.twilio.com/2010-04-01/Accounts/ACc1fc2722e4be778043d3e173c8ac7c58/Messages/MM86c45e60add9b2417ada10181b4dce05/Media/ME89515c12171959023af36e5ca3939cf7"
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
                'conversation_id' => $chat->id,
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
        $sender = $sender ?? config('services.twilio.whatsapp');

        return $this->client->messages->create(
            sprintf('whatsapp:%s', $recipient),
            [
                'from' => sprintf('whatsapp:%s', $sender),
                'body' => $body,
            ]
        );
    }
}
