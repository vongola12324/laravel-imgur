<?php

namespace Vongola\Imgur\Auth;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Vongola\Imgur\Exceptions\AuthException;
use Vongola\Imgur\HttpClient\HttpClient;

use function is_array;

class OAuth2
{
    const AUTHORIZATION_ENDPOINT = 'https://api.imgur.com/oauth2/authorize';
    const ACCESS_TOKEN_ENDPOINT = 'https://api.imgur.com/oauth2/token';

    private array $options = [];
    private HttpClient $httpClient;

    /**
     * The access token and refresh token values, with keys:.
     *
     * For "token":
     *     - access_token
     *     - expires_in
     *     - token_type
     *     - refresh_token
     *     - account_username
     *     - account_id
     *
     * For "code":
     *     - code
     *     - state
     *
     * For "pin":
     *     - pin
     *     - state
     *
     * @var array
     */
    private array $token;

    /**
     * Instantiates the OAuth2 class, but does not trigger the authentication process.
     *
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct(HttpClient $httpClient, string $clientId, string $clientSecret)
    {
        $this->options['clientId'] = $clientId;
        $this->options['clientSecret'] = $clientSecret;
        $this->httpClient = $httpClient;
    }

    /**
     * Generates the authentication URL to which a user should be pointed at in order to start the OAuth2 process.
     *
     * @param string $responseType
     * @param string|null $state
     *
     * @return string
     */
    public function getAuthenticationURL(string $responseType = 'code', ?string $state = null): string
    {
        $httpQueryParameters = [
            'client_id'     => $this->options['clientId'],
            'response_type' => $responseType,
            'state'         => $state,
        ];

        $httpQueryParameters = http_build_query($httpQueryParameters);

        return self::AUTHORIZATION_ENDPOINT . '?' . $httpQueryParameters;
    }

    /**
     * Exchanges a code/pin for an access token.
     *
     * @param string $code
     * @param string $requestType
     *
     * @return array
     * @throws GuzzleException
     */
    public function requestAccessToken(string $code, string $requestType): array
    {
        switch ($requestType) {
            case 'pin':
                $grantType = 'pin';
                $type = 'pin';
                break;
            case 'code':
            default:
                $grantType = 'authorization_code';
                $type = 'code';
        }

        try {
            $response = $this->httpClient->post(
                self::ACCESS_TOKEN_ENDPOINT,
                [
                    'client_id'     => $this->options['clientId'],
                    'client_secret' => $this->options['clientSecret'],
                    'grant_type'    => $grantType,
                    $type           => $code,
                ]
            );

            $responseBody = json_decode($response->getBody(), true);
        } catch (Exception $e) {
            throw new AuthException('Request for access token failed: ' . $e->getMessage(), $e->getCode());
        }

        $responseBody['created_at'] = time();
        $this->setAccessToken($responseBody);

        $this->sign();

        return $responseBody;
    }

    /**
     * If a user has authorized their account but you no longer have a valid access_token for them,
     * then a new one can be generated by using the refresh_token.
     * When your application receives a refresh token, it is important to store that refresh token for future use.
     * If your application loses the refresh token,
     * you will have to prompt the user for their login information again.
     *
     * @return array
     * @throws GuzzleException
     */
    public function refreshToken(): array
    {
        $token = $this->getAccessToken();

        try {
            $response = $this->httpClient->post(
                self::ACCESS_TOKEN_ENDPOINT,
                [
                    'refresh_token' => is_array($token) ? $token['refresh_token'] : null,
                    'client_id'     => $this->options['clientId'],
                    'client_secret' => $this->options['clientSecret'],
                    'grant_type'    => 'refresh_token',
                ]
            );

            $responseBody = json_decode($response->getBody(), true);
        } catch (Exception $e) {
            throw new AuthException('Request for refresh access token failed: ' . $e->getMessage(), $e->getCode());
        }

        $this->setAccessToken($responseBody);

        $this->sign();

        return $responseBody;
    }

    /**
     * Stores the access token, refresh token and expiration date.
     *
     * @param array $token
     *
     * @return void
     * @throws AuthException
     *
     */
    public function setAccessToken(array $token)
    {
        if (!is_array($token)) {
            throw new AuthException('Token is not a valid json string.');
        }

        if (isset($token['data']['access_token'])) {
            $token = $token['data'];
        }

        if (!isset($token['access_token'])) {
            throw new AuthException('Access token could not be retrieved from the decoded json response.');
        }

        $this->token = $token;

        $this->sign();
    }

    /**
     * Getter for the current access token.
     *
     * @return array|null
     */
    public function getAccessToken(): ?array
    {
        return $this->token;
    }

    /**
     * Check if the current access token (if present), is still usable.
     *
     * @return bool
     */
    public function checkAccessTokenExpired(): bool
    {
        // don't have the data? Let's assume the token has expired
        if (!isset($this->token['created_at']) || !isset($this->token['expires_in'])) {
            return true;
        }

        return ($this->token['created_at'] + $this->token['expires_in']) < time();
    }

    /**
     * Add middleware for attaching header signature to each request.
     */
    public function sign()
    {
        $this->httpClient->addAuthMiddleware(
            $this->getAccessToken(),
            $this->options['clientId']
        );
    }
}