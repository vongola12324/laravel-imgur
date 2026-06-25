<?php

declare(strict_types=1);

namespace Vongola\ImgurTests\Client;

use Imgur\Api\Account;
use Imgur\Api\Album;
use Imgur\Api\Comment;
use Imgur\Api\Gallery;
use Imgur\Api\Image;
use InvalidArgumentException;
use Vongola\Imgur\Client as ImgurClient;
use Vongola\ImgurTests\TestCase;

class ClientTest extends TestCase
{
    /** @dataProvider apiClassProvider */
    public function testApiCallReturnsCorrectInstance(string $method, string $expectedClass): void
    {
        $client = new ImgurClient();
        $this->assertInstanceOf($expectedClass, $client->$method());
    }

    public static function apiClassProvider(): array
    {
        return [
            ['account', Account::class],
            ['album',   Album::class],
            ['comment', Comment::class],
            ['gallery', Gallery::class],
            ['image',   Image::class],
        ];
    }

    public function testInvalidApiNameThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $client = new ImgurClient();
        $client->doNotExist();
    }

    public function testGetAuthenticationUrlDefault(): void
    {
        $client = new ImgurClient();
        $url = $client->getAuthenticationUrl();
        $this->assertStringContainsString('client_id=123', $url);
        $this->assertStringContainsString('response_type=code', $url);
    }

    public function testGetAuthenticationUrlWithPin(): void
    {
        $client = new ImgurClient();
        $url = $client->getAuthenticationUrl('pin');
        $this->assertStringContainsString('response_type=pin', $url);
    }

    public function testGetAuthenticationUrlWithState(): void
    {
        $client = new ImgurClient();
        $url = $client->getAuthenticationUrl('code', 'draft');
        $this->assertStringContainsString('state=draft', $url);
    }

    public function testGetAccessTokenReturnsNullInitially(): void
    {
        $client = new ImgurClient();
        $this->assertNull($client->getAccessToken());
    }

    public function testCheckAccessTokenExpiredReturnsTrueWhenNoToken(): void
    {
        $client = new ImgurClient();
        $this->assertTrue($client->checkAccessTokenExpired());
    }
}
