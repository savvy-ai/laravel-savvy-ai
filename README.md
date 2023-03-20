# Savvy AI
Domain knowledge artificial intelligence framework for Laravel

## Usage

### Installation
```bash
composer install savvy-ai/laravel-savvy-ai
```

### Configure
```
php artisan vendor:publish --tag="savvy-ai-config"

// Edit config file
config/savvy-ai.php
```

## Concerns
name|explanation|default
----|-----------|-------
ManagesTrainables|Can have trainables attached to it| `App\User`
Trainables|Can be trained and have statements| `App\Property`
Chattables|Can have chats| `App\Property`

## Classes
name|explanation|default
----|-----------|-------
Statement|Generated from a trained session and attached to the `Trainable`|`App\Statement`
Chatbot|Manages a conversation| `App\Chatbot`
Agent|Classifies and delegates to dialogues| `App\Agent`
Dialogue|Generates a reply for the given message| `App\Dialogue`
Chat|Can have messages attached to it| `App\Chat`
Messages|Can be attached to a chat|`App\Message`
Reply|A special kind of message|`App\Reply`

## Gotchas 💣
> We use `uuids` everywhere!
