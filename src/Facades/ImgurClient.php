<?php

namespace Vongola\Imgur\Facades;

use Illuminate\Support\Facades\Facade;

class Upload extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Vongola\Imgur\ImgurClient::class;
    }
}