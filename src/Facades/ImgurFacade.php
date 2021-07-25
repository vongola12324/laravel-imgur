<?php

namespace Vongola\Imgur\Facades;

use Illuminate\Support\Facades\Facade;
use Vongola\Imgur\Client;

class ImgurFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Client::class;
    }
}