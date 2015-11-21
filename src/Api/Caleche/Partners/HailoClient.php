<?php
/**
 * Default controller for base endpoints.
 */

namespace Api\Caleche\Partners;

use Api\Application;
use GuzzleHttp\Client as HttpClient;

class HailoClient
{

    private $access_token = "iJgAldWT75/xfN7UrhupbKU+DN7foAU3I6uyMiGxJ1PLcWCpWbHyjP7E2ybH+BMmYo4Gh9i7ABbi7ZnYL8Dxf4NUiRaHwl0YKwp97+bR1ZFmmH1Rovc5vJzSD4ASfonh2KMFsv6ERZUwHcOBbUCQDjPjvoOiNyg/k9v+Ab8N2q4LUEY9fQwu3N1djz/LFGxamw+zoK6xMlYHyOVkKob93A==";
    private $http_client;

    private $api_url ='https://api.hailoapp.com/';

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
    public function getPriceEstimates($times, $distance)
    {
        $price = 3.625*$distance+2.5;
        foreach ($times->etas as $hailo) {
            $hailo->price = $price;
            $hailo->type = "hailo";
        }
        return $times->etas;
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
        $attributes['api_token'] = $this->access_token;
        return $this->request('get', 'drivers/eta', $attributes);
    }



    public function mixPriceTimeEstimates($times, $prices){
        foreach($times->times as $time){
            foreach ($prices->prices as $cab) {
                if($time->product_id == $cab->product_id){
                    $cab->time_estimate = $time->estimate;
                }
            }
        }

        return $prices;
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
