<?php

namespace Vongola\ImgurTests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Vongola\Imgur\ImgurServiceProvider;

class TestCase extends BaseTestCase
{
    protected bool $loadEnvironmentVariables = true;

    public function setUp(): void
    {
        parent::setUp();
        // additional setup
    }

    protected function getPackageProviders($app): array
    {
        return [
            ImgurServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->config->set('imgur.client_id', '123');
        $app->config->set('imgur.client_secret', '123');
    }
}