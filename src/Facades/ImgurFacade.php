<?php

declare(strict_types=1);

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
