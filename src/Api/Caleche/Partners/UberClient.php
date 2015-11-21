<?php
/**
 * Default controller for base endpoints.
 */

namespace Api\Caleche\Partners;

use Api\Application;
use GuzzleHttp\Client as HttpClient;

class UberClient
{

    private $acess_token;
    private $server_token;
    private $client_id;
    private $app_id;
    private $http_client;
    private $use_sandbox;

    private $api_url ='https://api.uber.com/v1/';
    private $default_productId;

    private $locale = 'en_GB';

    public function __construct($config){

        $this->server_token = $config['server_token'];
        $this->client_id = $config['client_id'];
        $this->app_id = $config['app_id'];
        $this->use_sandbox = $config['use_sandbox'];
        $this->http_client = new HttpClient;
    }


    /**
     * The Price Estimates endpoint returns an estimated price range for each
     * product offered at a given location. The price estimate is provided as
     * a formatted string with the full price range and the localized currency
     * symbol.
     *
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     */
    public function getPriceEstimates($location)
    {
        return $this->request('get', 'estimates/price', $location);
    }

     /**
     * Build url
     *
     * @param  string   $path
     *
     * @return string   Url
     */
    public function getUrlFromPath($path)
    {

        if ($this->use_sandbox) {
            return 'https://sandbox-api.uber.com/v1/'.$path;
        }

        return 'https://api.uber.com/v1/'.$path;
    }


    /**
     * Makes a request to the Uber API and returns the response
     *
     * @param    string $verb       The Http verb to use
     * @param    string $path       The path of the APi after the domain
     * @param    array  $parameters Parameters
     *
     * @return   stdClass The JSON response from the request
     * @throws   Exception
     */
    private function request($verb, $path, $parameters = [])
    {
        $client = $this->http_client;
        $url = $this->getUrlFromPath($path);
        $verb = strtolower($verb);
        $config = $this->getConfigForVerbAndParameters($verb, $parameters);
        try {
            $response = $client->$verb($url, $config);

        } catch (HttpClientException $e) {
            throw new Exception($e->getMessage());
        }

        return json_decode($response->getBody());
    }

    /**
     * Get HttpClient config for verb and parameters
     *
     * @param  string $verb
     * @param  array  $parameters
     *
     * @return array
     */
    private function getConfigForVerbAndParameters($verb, $parameters = [])
    {
        $config = [
            'headers' => $this->getHeaders()
        ];

        if (!empty($parameters)) {
            if (strtolower($verb) == 'get') {
                $config['query'] = $parameters;
            } else {
                $config['json'] = $parameters;
            }
        }

        return $config;
    }

    /**
     * Get authorization header value
     *
     * @return string
     */
    private function getAuthorizationHeader()
    {
        if ($this->access_token) {
            return 'Bearer '.$this->access_token;
        }

        return 'Token '.$this->server_token;
    }

    /**
     * Get headers for request
     *
     * @return array
     */
    public function getHeaders()
    {
        return [
            'Authorization' => $this->getAuthorizationHeader(),
            'Accept-Language' => $this->locale,
        ];
    }




    /**
     * Return stats info API.
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function info(Application $app)
    {
        return $app->json(array(
            'name'      => $app['config']['name'],
            'version'   => $app['config']['version'],
            'source'    => $app['config']['sourceVersion'],
            'env'       => $app['environment'],
            'debug'     => $app['debug'],
        ));
    }

}
