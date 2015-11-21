<?php
/**
 * Default controller for base endpoints.
 */

namespace Api\Caleche\Partners;

use Api\Application;
use GuzzleHttp\Client as HttpClient;


class TaxiCodeClient
{

	private $http_client;

    private $api_url ='https://api.taxicode.com/';

    private $locale = 'en_GB';

    public function __construct(){

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
    public function getBookingQuote($location)
    {
        $results =  $this->request('get', 'booking/quote/', $location);

        $result_quotes = [];

        foreach ($results->quotes as $quote) {
        	$quote->type="taxicode";
        	$quote->eta = 60;
        	$result_quotes [] = $quote;
        }
        return $result_quotes;
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

        return $this->api_url.$path;
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

        if (!empty($parameters)) {
            if (strtolower($verb) == 'get') {
                $config['query'] = $parameters;
            } else {
                $config['json'] = $parameters;
            }
        }

        return $config;
    }





}
