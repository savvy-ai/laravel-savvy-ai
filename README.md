# Savvy AI for Laravel
Domain knowledge artificial intelligence framework for Laravel

## Usage

### Install
```bash
# install package
composer require savvy-ai/laravel-savvy-ai

# export migrations
php artisan vendor:publish --tag="savvy-ai-migrations"
```

### Configure
```bash
# export config
php artisan vendor:publish --tag="savvy-ai-config"

# edit config
config/savvy-ai.php
```

### Filament
> If you're using filament in your Laravel app, you can export the resources provided by Savvy AI.
>
```bash
php artisan vendor:publish --tag="savvy-ai-filament"
```

## Gotchas 💣
> We use `uuids` everywhere!
