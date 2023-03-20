<?php

namespace SavvyAI\Savvy\Chat\Tools;

use SavvyAI\Models\Chat;
use SavvyAI\Models\Message;
use SavvyAI\Savvy\Chat\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class GetCurrentWeather
{
    public function use(Chat $chat, Message $incomingMessage): Message
    {
        $property = $chat->property;

        $response = Http::timeout(3)
            ->acceptJson()
            ->get(config('services.weather.url'), [
                'key'  => config('services.weather.key'),
                'q'    => sprintf('%s,%s', $property->latitude, $property->longitude),
                'days' => 5,
            ])->json();

        $temperature = $response['current']['temp_f'] ?? '';
        $condition   = $response['current']['condition']['text'] ?? '';

        $forecast = collect($response['forecast']['forecastday'])->map(function ($day)
        {
            $dateAsDay = Carbon::parse($day['date'])->format('D');

            return [
                'date'      => $dateAsDay,
                'max_temp'  => round($day['day']['maxtemp_f']),
                'min_temp'  => round($day['day']['mintemp_f']),
                'condition' => $day['day']['condition']['text'],
                'icon'      => $day['day']['condition']['icon'],
            ];
        });

        return new Message([
            'role' => Role::Assistant,
            'content' => sprintf(
                'Today\'s temperature is %s ℉ with %s conditions.',
                $temperature, $condition
            ),
            'media' => ['forcast' => $forecast->toArray()],
        ]);
    }
}
