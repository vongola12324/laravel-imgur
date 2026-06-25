<?php

declare(strict_types=1);

namespace Vongola\Imgur;

use Imgur\Client as ImgurClient;

class Client
{
    private ImgurClient $imgur;

    public function __construct()
    {
        $this->imgur = new ImgurClient;
        $this->imgur->setOption('client_id', config('imgur.client_id'));
        $this->imgur->setOption('client_secret', config('imgur.client_secret'));
    }

    public function __call(string $name, array $argv): mixed
    {
        return $this->imgur->api($name);
    }

    public static function __callStatic(string $name, array $argv): mixed
    {
        return (new self)->$name(...$argv);
    }

    public function getAuthenticationUrl(string $responseType = 'code', ?string $state = null): string
    {
        return $this->imgur->getAuthenticationUrl($responseType, $state);
    }

    public function requestAccessToken(string $code, string $responseType = 'code'): array
    {
        return $this->imgur->requestAccessToken($code, $responseType);
    }

    public function getAccessToken(): ?array
    {
        return $this->imgur->getAccessToken();
    }

    public function setAccessToken(array $token): void
    {
        $this->imgur->setAccessToken($token);
    }

    public function refreshToken(): array
    {
        return $this->imgur->refreshToken();
    }

    public function checkAccessTokenExpired(): bool
    {
        return $this->imgur->checkAccessTokenExpired();
    }

    public function sign(): void
    {
        $this->imgur->sign();
    }
}
