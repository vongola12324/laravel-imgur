<?php

namespace Vongola\Imgur\HttpClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Vongola\Imgur\Middleware\AuthMiddleware;
use Vongola\Imgur\Middleware\ErrorMiddleware;

class HttpClient
{
    /**
     * The Guzzle instance.
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     * HTTP Client Settings.
     *
     * @var array
     */
    protected array $options;

    /**
     * Guzzle Handler Stack
     *
     * @var HandlerStack
     */
    protected HandlerStack $stack;

    public function __construct(?GuzzleClient $client = null, array $options = [])
    {
        $this->options = $options;

        $this->stack = HandlerStack::create();
        $this->stack->push(ErrorMiddleware::error());

        $this->client = $client ?? new GuzzleClient(
            [
                'base_uri' => $this->options['base_url'],
                'handler'  => $this->stack,
            ]
        );
    }

    /**
     * @param $url
     * @param array $parameters
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function get($url, array $parameters = []): ResponseInterface
    {
        return $this->performRequest($url, $parameters);
    }

    /**
     * @param $url
     * @param array $parameters
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function delete($url, array $parameters = []): ResponseInterface
    {
        return $this->performRequest($url, $parameters, 'DELETE');
    }

    /**
     * @param $url
     * @param array $parameters
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function post($url, array $parameters = []): ResponseInterface
    {
        return $this->performRequest($url, $parameters, 'POST');
    }

    /**
     * @param $url
     * @param array $parameters
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function put($url, array $parameters = []): ResponseInterface
    {
        return $this->performRequest($url, $parameters, 'PUT');
    }


    /**
     * @param $url
     * @param $parameters
     * @param string $httpMethod
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function performRequest($url, $parameters, string $httpMethod = 'GET'): ResponseInterface
    {
        $options = [
            'headers' => $this->options['headers'] ?? [],
            'body'    => $this->options['body'] ?? '',
        ];

        if (isset($parameters['query'])) {
            $options['query'] = $parameters['query'];
        }

        if ('POST' === $httpMethod || 'PUT' === $httpMethod || 'DELETE' === $httpMethod) {
            if ('POST' === $httpMethod && isset($parameters['type']) && 'file' === $parameters['type']) {
                $options['multipart'] = [
                    [
                        'name'     => 'type',
                        'contents' => $parameters['type'],
                    ],
                    [
                        'name'     => 'image',
                        'contents' => $parameters['image'],
                    ],
                ];
            } else {
                $options['form_params'] = $parameters;
            }
        }

        // will throw an GuzzleException if something goes wrong
        return $this->client->request($httpMethod, $url, $options);
    }

    /**
     * @param $response
     * @return mixed
     */
    public function parseResponse($response)
    {
        $responseBody = ['data' => [], 'success' => false];

        if ($response) {
            $responseBody = json_decode($response->getBody(), true);
        }

        return $responseBody['data'];
    }

    /**
     * Push authorization middleware.
     *
     * @param array $token
     * @param string $clientId
     */
    public function addAuthMiddleware(array $token, string $clientId)
    {
        $this->stack->push(
            Middleware::mapRequest(
                function (RequestInterface $request) use ($token, $clientId) {
                    return (new AuthMiddleware($token, $clientId))->addAuthHeader($request);
                }
            )
        );
    }
}
