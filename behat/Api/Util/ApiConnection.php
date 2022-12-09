<?php

namespace Api\Util;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Response;

/**
 * Class ApiConnection
 * Helper class. Handle all works with app api.
 *
 * @package Api\Util
 */
class ApiConnection
{

    /**
     * Used front controller.
     */
    const FRONT_CONTROLLER = 'app_test.php';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var boolean
     */
    private $debug;

    /**
     * @var Response
     */
    private $lastResponse;

    /**
     * @var null|string
     */
    private $lastResponseData;

    /**
     * ApiConnection constructor.
     *
     * @param string  $baseUrl Base url to app api.
     * @param boolean $debug   Print debug information if set.
     */
    public function __construct($baseUrl, $debug)
    {
        $cookies = false;

        if ($debug) {
            $cookies = CookieJar::fromArray([
                'XDEBUG_SESSION' => 'PHPSTORM',
            ], parse_url($baseUrl, PHP_URL_HOST));
        }

        $this->client = new Client([
            'base_uri' => $baseUrl,
            'cookies' => $cookies,
        ]);
        $this->debug = $debug;
    }

    /**
     * Make request to api.
     *
     * @param string $method    HTTP method name.
     * @param string $endpoint  Relative to base url.
     * @param array  $payload   Request payload, may send as query string or as
     *                          json relative to method name.
     * @param string $authToken Application authentication token.
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function request(
        $method,
        $endpoint,
        array $payload = [],
        $authToken = null
    ) {
        $method = strtoupper($method);

        // Prepare full endpoint url.
        $endpoint = self::FRONT_CONTROLLER . '/' . ltrim($endpoint, '/');

        // Prepare request options.
        $options = [];
        // Add authorization token if it provided.
        if ($authToken !== null) {
            $options['headers'] = [ 'Authorization' => 'Bearer '. $authToken ];
        }

        // Send payload as query string for 'GET' request.
        // For other methods, send in content as json.
        $options[($method === 'GET') ? 'query' : 'json'] = $payload;

        // Show debug information about request.
        if ($this->debug) {
            echo 'Request: ' . $this->client->getConfig('base_uri')
                . $endpoint. PHP_EOL;
            echo 'Request options: ' . PHP_EOL
                . json_encode($options, JSON_PRETTY_PRINT)
                . PHP_EOL;
        }

        try {
            // Make request to api and save response to class.
            $this->lastResponseData = null;
            $this->lastResponse =
                $this->client->request($method, $endpoint, $options);
        } catch (BadResponseException $e) {
            $this->lastResponse = $e->getResponse();
        }

        if ($this->debug) {
            $decodedResponse = json_decode($this->getLastResponseData(), true);

            if ($decodedResponse) {
                // In debug mode dump data received from server to output.
                print('Response data: ' . PHP_EOL
                    . json_encode(
                        $decodedResponse,
                        JSON_PRETTY_PRINT
                    ). PHP_EOL
                );
            } else {
                print $this->getLastResponseData().PHP_EOL;
            }
        }

        return $this->lastResponse;
    }

    /**
     * Get last response.
     *
     * @return Response
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Get data from last response.
     *
     * @return null|string
     */
    public function getLastResponseData()
    {
        if (($this->lastResponseData === null) && $this->lastResponse) {
            $this->lastResponseData = $this->lastResponse
                ->getBody()
                ->getContents();
        }

        return $this->lastResponseData;
    }
}
