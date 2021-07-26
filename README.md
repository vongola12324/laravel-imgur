# laravel-imgur
Laravel wrapper for the Imgur API.  
Only work for Laravel 7.x (or above) and PHP 7.4 (or above).  
If you are using older version of Laravel, you should use [j0k3r/php-imgur-api-client](https://github.com/j0k3r/php-imgur-api-client).

## Installation
```
composer require vongola12324/laravel-imgur
```

## Config
You have to add **IMGUR_CLIENT_ID** and **IMGUR_CLIENT_SECRET** in your .env file, or client will not be able to work.  
See [Imgur Api Docs](https://apidocs.imgur.com/) for details.

## Usage
### Basic Usage
```php
// Create $imgurClient object
use Vongola\Imgur\Client as ImgurClient;
$imgurClient = new ImgurClient();
// The API calls can be accessed via the $imgurClient object
$imgurClient->memegen()->defaultMemes();
```
Also, You can use Facade instead of `new` class.  
```php
use Vongola\Imgur\Client as ImgurClient;
// The API calls can be accessed via the $imgurClient object
ImgurClient::memegen()->defaultMemes();
```
### Api
At this time we support the following Apis:
- Account (`$imgurClient->account()`)
- Album (`$imgurClient->album()`)
- Comment (`$imgurClient->comment()`)
- Gallery (`$imgurClient->gallery()`)
- Image (`$imgurClient->image()`)


See [Imgur Api Docs](https://apidocs.imgur.com/) for all Api.


## Notice
1. This project is based on [j0k3r/php-imgur-api-client](https://github.com/j0k3r/php-imgur-api-client).
2. There are some feature available in j0k3r's version, which do not show in [Imgur Api Docs](https://apidocs.imgur.com/), will not provide in this package.
3. The test case of this project are still WIP.
4. It is recommended not to use this project in a formal project (because it has not been officially released yet) unless you understand what you are doing!

## License
MIT License
