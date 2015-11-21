<?php
/**
 * Basic sample of a RESTful endpoint using static data.
 */
namespace Api\Caleche\Controllers;

use Api\Application;
use Api\Controller;
use Api\Caleche\Partners\UberClient;
use Api\Caleche\Partners\HailoClient;
use Api\Caleche\Partners\TaxicodeClient;
use \DateTime;

class CalecheController extends Controller
{

    private $uber_client;
    private $hailo_client;
    private $taxicode_client;

    public function request(Application $app)
    {
        if(!isset($uber_client)){
            $config['access_token'] = $app['uber_config']['access_token'];
            $config['server_token'] = $app['uber_config']['server_token'];
            $config['client_id'] = $$app['uber_config']['client_id'];
            $config['app_id'] = $app['uber_config']['app_id'];

            $uber_client = new UberClient($config);
        }

        if(!isset($hailo_client)){

            $hailo_client = new HailoClient();
        }

        if(!isset($taxicode_client)){
            $taxicode_client = new TaxicodeClient();
        }

        $data = json_decode($app['request']->getContent());
        //UBER
        $location = array(
            'start_latitude'=> $data->start_latitude,
            'start_longitude' => $data->start_longitude,
            'end_latitude' => $data->end_latitude,
            'end_longitude'=> $data->end_longitude
        );

        $prices = $uber_client->getPriceEstimates($location);

        $start_location = array(
            'start_latitude'=> $data->start_latitude,
            'start_longitude' => $data->start_longitude
        );

        $times = $uber_client->getTimeEstimates($start_location);

        $uber = $uber_client->mixPriceTimeEstimates($times, $prices);

        //HAILO

        $location = array(
            'latitude'=> $data->start_latitude,
            'longitude' => $data->start_longitude
        );

        $hailo_time = $hailo_client->getTimeEstimates($location);
        $hailo = $hailo_client->getPriceEstimates($hailo_time, $uber[0]->distance);


        //TAXICODE

        $date = new DateTime();
        $now = $date->getTimestamp()+1000;

        $taxicode_location = array(
            'pickup' => $data->start_latitude .','.$data->start_longitude,
            'destination' =>$data->end_latitude .','.$data->end_longitude,
            'date' => $now
            );

        $taxicode = $taxicode_client->getBookingQuote($taxicode_location);

        //var_dump($taxicode);
        $response =[];
        $response['uber'] = $uber;
        $response['taxicode'] = $taxicode;
        $response['hailo'] = $hailo;

        return $app->json($response);
    }

    public function get(Application $app, $id)
    {
        return $app->json(array('id' => (int)$id, 'name' => 'gumby'.$id));
    }



}
