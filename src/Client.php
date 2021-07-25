<?php

namespace Vongola\Imgur;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use RuntimeException;
use Vongola\Imgur\Api\Account;
use Vongola\Imgur\Auth\OAuth2;
use Vongola\Imgur\HttpClient\HttpClient;

/**
 * Class Client
 * @package Vongola\Imgur
 * @method static Account account
 */
class Client
{
    /**
     * Client options
     * @var array
     */
    private array $options = [
        'base_url'      => 'https://api.imgur.com/3/',
        'client_id'     => null,
        'client_secret' => null,
    ];

    /**
     * Guzzle-Base HttpClient
     * @var HttpClient
     */
    private HttpClient $httpClient;

    /**
     * The class handling authentication.
     *
     * @var OAuth2
     */
    private OAuth2 $authenticationClient;

    /**
     * Client constructor.
     */
    public function __construct()
    {
        $this->loadConfig();
        $this->httpClient = new HttpClient(Arr::only($this->options, ['base_url']));
        $this->authenticationClient = new OAuth2(
            $this->httpClient,
            $this->getOption('client_id'),
            $this->getOption('client_secret')
        );
    }

    /**
     * Call Api
     */
    public function __call($name, $argv)
    {
        if (!$this->getAccessToken()) {
            $this->sign();
        }

        $apiClass = 'Vongola\\Imgur\\Api\\' . ucfirst($name);
        if (class_exists($apiClass)) {
            return new $apiClass($this, $argv);
        }

        throw new InvalidArgumentException('API Method not supported: "' . $name . '" (apiClass: "' . $apiClass . '")');
    }

    public static function __callStatic($name, $argv)
    {
        return call_user_func(new(static::class), $name, $argv);
    }

    private function loadConfig()
    {
        if (!config('imgur.client_id') || !config('imgur.client_secret')) {
            throw new RuntimeException('Client client id or secret is empty.');
        }
        $this->setOption('client_id', config('imgur.client_id'));
        $this->setOption('client_secret', config('imgur.client_secret'));
    }

    /**
     * @param string $name
     * @return string
     * @throws InvalidArgumentException
     */
    public function getOption(string $name): string
    {
        if (!array_key_exists($name, $this->options)) {
            throw new InvalidArgumentException(sprintf('Undefined option called: "%s"', $name));
        }
        return $this->options[$name];
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws InvalidArgumentException
     */
    public function setOption(string $name, $value)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new InvalidArgumentException(sprintf('Undefined option called: "%s"', $name));
        }
        $this->options[$name] = $value;
    }


    /**
     * Proxy method for the authentication objects URL building method.
     *
     * @param string $responseType
     * @param string|null $state
     *
     * @return string
     */
    public function getAuthenticationUrl(string $responseType = 'code', ?string $state = null): string
    {
        return $this->authenticationClient->getAuthenticationUrl($responseType, $state);
    }

    /**
     * Proxy method for exchanging a code for an access token/a pin for an access token.
     *
     * @param string $code
     * @param string $responseType
     *
     * @return array
     */
    public function requestAccessToken(string $code, string $responseType = 'code'): array
    {
        return $this->authenticationClient->requestAccessToken($code, $responseType);
    }

    /**
     * Proxy method for retrieving the access token.
     *
     * @return array
     */
    public function getAccessToken(): array
    {
        return $this->authenticationClient->getAccessToken();
    }

    /**
     * Proxy method for checking if the access token expired.
     *
     * @return bool
     */
    public function checkAccessTokenExpired(): bool
    {
        return $this->authenticationClient->checkAccessTokenExpired();
    }

    /**
     * Proxy method for refreshing an access token.
     *
     * @return array
     * @throws GuzzleException
     */
    public function refreshToken(): array
    {
        return $this->authenticationClient->refreshToken();
    }

    /**
     * Proxy method for setting an access token.
     *
     * @param array $token
     */
    public function setAccessToken(array $token)
    {
        $this->authenticationClient->setAccessToken($token);
    }

    /**
     * Proxy method for signing a request.
     */
    public function sign()
    {
        $this->authenticationClient->sign();
    }
}