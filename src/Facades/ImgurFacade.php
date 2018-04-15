<?php

namespace Vongola\Imgur\Facades;

use Illuminate\Support\Facades\Facade;

class ImgurFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Vongola\Imgur\ImgurClient::class;
    }
}