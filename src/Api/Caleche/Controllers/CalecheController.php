<?php
/**
 * Basic sample of a RESTful endpoint using static data.
 */
namespace Api\Caleche\Controllers;

use Api\Application;
use Api\Controller;
use Api\Caleche\Partners\UberClient;

class CalecheController extends Controller
{

    private $uber_client;

    public function request(Application $app)
    {
        if(!isset($uber_client)){
            $config['access_token'] = $app['uber_config']['access_token'];
            $config['server_token'] = $app['uber_config']['server_token'];
            $config['client_id'] = $$app['uber_config']['client_id'];
            $config['app_id'] = $app['uber_config']['app_id'];

            $uber_client = new UberClient($config);
        }

        $data = json_decode($app['request']->getContent());

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

        $response = $uber_client->mixPriceTimeEstimates($times, $prices);

        return $app->json($response);
    }

    public function get(Application $app, $id)
    {
        return $app->json(array('id' => (int)$id, 'name' => 'gumby'.$id));
    }



}
