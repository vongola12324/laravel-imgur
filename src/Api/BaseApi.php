<?php

namespace Vongola\Imgur\Api;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Vongola\Imgur\HttpClient\HttpClient;
use Vongola\Imgur\Pager\Pager;

abstract class BaseApi
{
    /**
     * @var HttpClient
     */
    protected HttpClient $httpClient;

    /**
     * @var ?Pager
     */
    protected ?Pager $pager;

    public function __construct(HttpClient $httpClient, Pager $pager = null)
    {
        $this->httpClient = $httpClient;
        $this->pager = $pager;
    }

    /**
     * Perform a GET request and return the parsed response.
     *
     * @param string $url
     * @param array $parameters
     * @return mixed
     * @throws GuzzleException
     */
    protected function get(string $url, array $parameters = [])
    {
        if (!empty($this->pager)) {
            $parameters['page'] = $this->pager->getPage();
            $parameters['perPage'] = $this->pager->getResultsPerPage();
        }
        $response = $this->httpClient->get($url, ['query' => $parameters]);
        return $this->httpClient->parseResponse($response);
    }

    /**
     * Perform a POST request and return the parsed response.
     *
     * @param string $url
     * @param array $parameters
     * @return mixed
     * @throws GuzzleException
     */
    protected function post(string $url, array $parameters = [])
    {
        $response = $this->httpClient->post($url, $parameters);
        return $this->httpClient->parseResponse($response);
    }

    /**
     * Perform a PUT request and return the parsed response.
     *
     * @param string $url
     * @param array $parameters
     * @return mixed
     * @throws GuzzleException
     */
    protected function put(string $url, array $parameters = [])
    {
        $response = $this->httpClient->put($url, $parameters);
        return $this->httpClient->parseResponse($response);
    }

    /**
     * Perform a DELETE request and return the parsed response.
     *
     * @param string $url
     * @param array $parameters
     * @return mixed
     * @throws GuzzleException
     */
    protected function delete(string $url, array $parameters = [])
    {
        $response = $this->httpClient->delete($url, $parameters);
        return $this->httpClient->parseResponse($response);
    }

    /**
     * Validate "sort" parameter and throw an exception if it's a bad value.
     *
     * @param string $sort Input value
     * @param array $possibleValues
     */
    protected function validateSortArgument(string $sort, array $possibleValues)
    {
        $this->validateArgument('Sort', $sort, $possibleValues);
    }

    /**
     * Validate "window" parameter and throw an exception if it's a bad value.
     *
     * @param string $window Input value
     * @param array $possibleValues
     */
    protected function validateWindowArgument(string $window, array $possibleValues)
    {
        $this->validateArgument('Window', $window, $possibleValues);
    }

    /**
     * Validate "vote" parameter and throw an exception if it's a bad value.
     *
     * @param string $vote Input value
     * @param array $possibleValues
     */
    protected function validateVoteArgument(string $vote, array $possibleValues)
    {
        $this->validateArgument('Vote', $vote, $possibleValues);
    }

    /**
     * Global method to validate an argument.
     *
     * @param string $type The required parameter (used for the error message)
     * @param string $input Input value
     * @param array $possibleValues Possible values for this argument
     */
    private function validateArgument(string $type, string $input, array $possibleValues)
    {
        if (!in_array($input, $possibleValues, true)) {
            throw new InvalidArgumentException(
                $type . ' parameter "' . $input . '" is wrong. Possible values are: ' . implode(', ', $possibleValues)
            );
        }
    }
}