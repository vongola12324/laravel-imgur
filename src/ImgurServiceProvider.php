<?php

namespace Vongola\Imgur;

use Illuminate\Support\ServiceProvider;

class ImgurServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/imgur.php' => config_path('imgur.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../config/imgur.php',
            'imgur'
        );
    }
    /**
     * Register the application services.
     *
     */
    public function register()
    {
        $this->app->bind(Client::class, function ($app) {
            return new Client();
        });
    }
}
