<?php
/**
 * Default controller for base endpoints.
 */

namespace Api\Caleche\Partners;

use Api\Application;
use GuzzleHttp\Client as HttpClient;
use \DateTime;

class CitymapperClient
{

    private $api_key;
    private $http_client;

    private $api_url ='https://developer.citymapper.com/api/1/';

    private $locale = 'en_GB';

    public function __construct($key){

        $this->api_key = $key;

        $this->http_client = new HttpClient;
    }


    public function getTimeTravel($location)
    {
        $datetime = new DateTime('2010-12-30 23:21:46');

        $parameters = array(
            'startcoord' => $location['start_latitude'] .",". $location['start_longitude'],
            'endcoord' => $location['end_latitude'] .",". $location['end_longitude'],
            'time' => $datetime->format(DateTime::ISO8601),
            'time_type' =>'arrival',
            'key' => $this->api_key
        );

        return $this->request('get', 'traveltime/', $parameters);
    }





    /**
     * Makes a request to the Citymapper API and returns the response
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
        $url = $this->api_url.$path;
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