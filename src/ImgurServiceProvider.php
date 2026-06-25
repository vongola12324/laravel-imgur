<?php

declare(strict_types=1);

namespace Vongola\Imgur;

use Illuminate\Support\ServiceProvider;

class ImgurServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/imgur.php' => config_path('imgur.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/../config/imgur.php', 'imgur');
    }

    public function register(): void
    {
        $this->app->bind(Client::class, fn() => new Client());
    }
}
