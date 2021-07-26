<?php

namespace Vongola\ImgurTests\Client;

use InvalidArgumentException;
use Vongola\ImgurTests\TestCase;
use Vongola\Imgur\Api\Account;
use Vongola\Imgur\Api\Album;
use Vongola\Imgur\Api\Comment;
use Vongola\Imgur\Api\Gallery;
use Vongola\Imgur\Api\Image;
use Vongola\Imgur\Auth\OAuth2;
use Vongola\Imgur\Client as ImgurClient;
use Vongola\Imgur\HttpClient\HttpClient;

class ClientTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testNoParameters()
    {
        $client = new ImgurClient();
        $this->assertInstanceOf(HttpClient::class, $client->getHttpClient());
        $this->assertInstanceOf(OAuth2::class, $client->getAuthenticationClient());
    }

    public function testAuthenticationParameter()
    {
        $client = new ImgurClient(null, $this->getAuthenticationClientMock());
        $this->assertInstanceOf(HttpClient::class, $client->getHttpClient());
        $this->assertInstanceOf(OAuth2::class, $client->getAuthenticationClient());
    }

    public function testHttpParameter()
    {
        $client = new ImgurClient($this->getHttpClientMock(), null);
        $this->assertInstanceOf(HttpClient::class, $client->getHttpClient());
        $this->assertInstanceOf(OAuth2::class, $client->getAuthenticationClient());
    }

    public function testBothParameter()
    {
        $client = new ImgurClient($this->getHttpClientMock(), $this->getAuthenticationClientMock());
        $this->assertInstanceOf(HttpClient::class, $client->getHttpClient());
        $this->assertInstanceOf(OAuth2::class, $client->getAuthenticationClient());
    }

    /**
     * @dataProvider getApiClassesProvider
     */
    public function testGetApiInstance($apiName, $class)
    {
        $client = new ImgurClient($this->getHttpClientMock(), $this->getAuthenticationClientMock());
        $this->assertInstanceOf($class, call_user_func([$client, $apiName]));
    }

    public function getApiClassesProvider(): array
    {
        return [
            ['account', Account::class],
            ['album', Album::class],
            ['comment', Comment::class],
            ['gallery', Gallery::class],
            ['image', Image::class],
        ];
    }

    public function testNotGetApiInstance()
    {
        $this->expectException(InvalidArgumentException::class);
        $client = new ImgurClient($this->getHttpClientMock(), $this->getAuthenticationClientMock());
        $client->doNotExist();
    }

    public function testGetOptionNotDefined()
    {
        $this->expectException(InvalidArgumentException::class);
        $client = new ImgurClient();
        $client->getOption('do_not_exist');
    }

    public function testSetOptionNotDefined()
    {
        $this->expectException(InvalidArgumentException::class);
        $client = new ImgurClient();
        $client->setOption('do_not_exist', 'value');
    }

    /**
     * @dataProvider getOptions
     */
    public function testGetOption($option, $value)
    {
        $client = new ImgurClient();
        $client->setOption($option, $value);

        $this->assertSame($value, $client->getOption($option));
    }

    public function getOptions(): array
    {
        return [
            ['base_url', 'url'],
            ['client_id', 'id'],
            ['client_secret', 'secret'],
        ];
    }

    public function testGetAuthenticationUrl()
    {
        $client = new ImgurClient();
        $this->assertSame(
            'https://api.imgur.com/oauth2/authorize?client_id=123&response_type=code',
            $client->getAuthenticationUrl()
        );
        $this->assertSame(
            'https://api.imgur.com/oauth2/authorize?client_id=123&response_type=pin',
            $client->getAuthenticationUrl('pin')
        );
        $this->assertSame(
            'https://api.imgur.com/oauth2/authorize?client_id=123&response_type=code&state=draft',
            $client->getAuthenticationUrl('code', 'draft')
        );

        $this->assertSame(
            'https://api.imgur.com/oauth2/authorize?client_id=123&response_type=code',
            $client->getAuthenticationUrl()
        );
        $this->assertSame(
            'https://api.imgur.com/oauth2/authorize?client_id=123&response_type=code&state=draft',
            $client->getAuthenticationUrl('code', 'draft')
        );
    }

    public function testCheckAccessTokenExpired()
    {
        $authenticationClient = $this->getAuthenticationClientMock(['checkAccessTokenExpired']);
        $authenticationClient->expects($this->once())
            ->method('checkAccessTokenExpired')
            ->with();

        $client = new ImgurClient(null, $authenticationClient);
        $client->checkAccessTokenExpired();
    }

    public function testRequestAccessToken()
    {
        $httpClient = $this->getHttpClientMock();
        $authenticationClient = $this->getAuthenticationClientMock();
        $authenticationClient->expects($this->once())
            ->method('requestAccessToken')
            ->with('code', 'code');

        $client = new ImgurClient($httpClient, $authenticationClient);
        $client->requestAccessToken('code');
    }

    public function testRefreshToken()
    {
        $httpClient = $this->getHttpClientMock();
        $authenticationClient = $this->getAuthenticationClientMock();
        $authenticationClient->expects($this->once())
            ->method('refreshToken');

        $client = new ImgurClient($httpClient, $authenticationClient);
        $client->refreshToken();
    }

    public function testSetAccessToken()
    {
        $httpClient = $this->getHttpClientMock();
        $authenticationClient = $this->getAuthenticationClientMock();
        $authenticationClient->expects($this->once())
            ->method('setAccessToken')
            ->with(['token']);

        $client = new ImgurClient($httpClient, $authenticationClient);
        $client->setAccessToken(['token']);
    }

    private function getHttpClientMock()
    {
        $methods = ['get', 'post', 'put', 'delete', 'performRequest', 'parseResponse'];

        return $this->getMockBuilder(HttpClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods($methods)
            ->getMock();
    }

    private function getAuthenticationClientMock(array $methods = [])
    {
        $methods = array_merge(
            ['getAuthenticationUrl', 'getAccessToken', 'requestAccessToken', 'setAccessToken', 'sign', 'refreshToken'],
            $methods
        );

        return $this->getMockBuilder(OAuth2::class)
            ->disableOriginalConstructor()
            ->onlyMethods($methods)
            ->getMock();
    }
}
