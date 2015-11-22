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
     * The Time Estimates endpoint returns ETAs for all products offered at a
     * given location, with the responses expressed as integers in seconds. We
     * recommend that this endpoint be called every minute to provide the most
     * accurate, up-to-date ETAs.
     *
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     */
    public function getTimeEstimates($attributes = [])
    {
        return $this->request('get', 'estimates/time', $attributes);
    }


    /**
     * The Request Estimate endpoint allows a ride to be estimated given the
     * desired product, start, and end locations. If the end location is
     * not provided, only the pickup ETA and details of surge pricing
     * information are provided. If the pickup ETA is null, there are no cars
     * available, but an estimate may still be given to the user.
     *
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     */
    public function getRequestEstimate($attributes = [])
    {
        return $this->request('post', 'requests/estimate', $attributes);
    }

    /**
     * The Products endpoint returns information about the Uber products
     * offered at a given location. The response includes the display name and
     * other details about each product, and lists the products in the proper
     * display order.
     *
     * Some Products, such as experiments or promotions such as UberPOOL and
     * UberFRESH, will not be returned by this endpoint.
     *
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     */
    public function getProducts($attributes = [])
    {
        return $this->request('get', 'products', $attributes);
    }




    public function mixPriceTimeEstimates($times, $prices){

        $results = [];
        foreach($times->times as $time){
            foreach ($prices->prices as $cab) {
                if($time->product_id == $cab->product_id){
                    $cab->eta = round($time->estimate/60);
                    $cab->type = 'uber';
                    $cab->price = ($cab->high_estimate + $cab->minimum)/2;
                    $results [] = $cab;
                }
            }
        }

        return $results;
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
            throw new Exception("Uber" . $e->getMessage());
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

}
