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
use Api\Caleche\Partners\CitymapperClient;
use \DateTime;

class CalecheController extends Controller
{

    private $uber_client;
    private $hailo_client;
    private $taxicode_client;

    public function cheapest(Application $app){

        if(!isset($uber_client)){
            $config['access_token'] = $app['uber_config']['access_token'];
            $config['server_token'] = $app['uber_config']['server_token'];
            $config['client_id'] = $$app['uber_config']['client_id'];
            $config['app_id'] = $app['uber_config']['app_id'];

            $uber_client = new UberClient($config);
        }

        if(!isset($hailo_client)){

            $hailo_client = new HailoClient($app['hailo_key']);
        }

        if(!isset($taxicode_client)){
            $taxicode_client = new TaxicodeClient();
        }

        if(!isset($citymapper_client)){
            $citymapper_client = new CitymapperClient($app['citymapper_key']);
        }

        $data = json_decode($app['request']->getContent());
        
        //Citymapper

        $location = array(
            'start_latitude'=> $data->start_latitude,
            'start_longitude' => $data->start_longitude,
            'end_latitude' => $data->end_latitude,
            'end_longitude'=> $data->end_longitude
        );
       // var_dump("Citymapper");
        // try{
        //     //$citymapper = $citymapper_client->getTimeTravel($location);
        // }catch (HttpClientException $e) {
        //     $citymapper = [];
        // }
        

        //UBER

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
        $response = array_merge($uber, $taxicode, $hailo);
        usort($response, array($this, "price_sort"));
        $price_sorted = $response;
        usort($response, array($this, "time_sort"));
        $time_sorted = $response;

        return $app->json($price_sorted[0]->price!=0 ? $price_sorted[0] : $price_sorted[1]);
    }

    

    public function closest(Application $app){
         if(!isset($uber_client)){
            $config['access_token'] = $app['uber_config']['access_token'];
            $config['server_token'] = $app['uber_config']['server_token'];
            $config['client_id'] = $$app['uber_config']['client_id'];
            $config['app_id'] = $app['uber_config']['app_id'];

            $uber_client = new UberClient($config);
        }

        if(!isset($hailo_client)){

            $hailo_client = new HailoClient($app['hailo_key']);
        }

        if(!isset($taxicode_client)){
            $taxicode_client = new TaxicodeClient();
        }

        if(!isset($citymapper_client)){
            $citymapper_client = new CitymapperClient($app['citymapper_key']);
        }

        $data = json_decode($app['request']->getContent());
        
        //Citymapper

        $location = array(
            'start_latitude'=> $data->start_latitude,
            'start_longitude' => $data->start_longitude,
            'end_latitude' => $data->end_latitude,
            'end_longitude'=> $data->end_longitude
        );

        //UBER

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
        $response = array_merge($uber, $taxicode, $hailo);
        usort($response, array($this, "price_sort"));
        $price_sorted = $response;
        usort($response, array($this, "time_sort"));
        $time_sorted = $response;

        return $app->json(isset($time_sorted[0]->eta) ? $time_sorted[0] : $time_sorted[1]);
    }

    public function requestAll(Application $app)
    {
        if(!isset($uber_client)){
            $config['access_token'] = $app['uber_config']['access_token'];
            $config['server_token'] = $app['uber_config']['server_token'];
            $config['client_id'] = $$app['uber_config']['client_id'];
            $config['app_id'] = $app['uber_config']['app_id'];

            $uber_client = new UberClient($config);
        }

        if(!isset($hailo_client)){

            $hailo_client = new HailoClient($app['hailo_key']);
        }

        if(!isset($taxicode_client)){
            $taxicode_client = new TaxicodeClient();
        }

        if(!isset($citymapper_client)){
            $citymapper_client = new CitymapperClient($app['citymapper_key']);
        }

        $data = json_decode($app['request']->getContent());
        
        //Citymapper

        $location = array(
            'start_latitude'=> $data->start_latitude,
            'start_longitude' => $data->start_longitude,
            'end_latitude' => $data->end_latitude,
            'end_longitude'=> $data->end_longitude
        );
       // var_dump("Citymapper");
        // try{
        //     //$citymapper = $citymapper_client->getTimeTravel($location);
        // }catch (HttpClientException $e) {
        //     $citymapper = [];
        // }
        

        //UBER

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
        $response = array_merge($uber, $taxicode, $hailo);
        usort($response, array($this, "price_sort"));
        $price_sorted = $response;
        usort($response, array($this, "time_sort"));
        $time_sorted = $response;

        //HAAAACK! CHANGE!!!
        $results['cheapest'] = $price_sorted[0]->price!=0 ? $price_sorted[0] : $price_sorted[1] ;
        $results['closest'] = isset($time_sorted[0]->eta) ? $time_sorted[0] : $time_sorted[1] ;
        $results['others_price'] = isset($price_sorted[0]->price)? array_slice($price_sorted, 1) : array_slice($price_sorted, 2);
        $results['others_time'] = isset($time_sorted[0]->price)? array_slice($time_sorted, 1) : array_slice($time_sorted, 2);
        //$results['citymapper'] = $citymapper;

        return $app->json($results);
    }

    public function price_sort($a, $b)
        {
        if ($a->price > $b->price) {
            return 1;
        } else if ($a->price < $b->price) {
            return -1;
        } else {
            return 99; 
        }
    }


    public function time_sort($a, $b)
        {
            if ($a->eta > $b->eta) {
                return 1;
            } else if ($a->eta < $b->eta) {
                return -1;
            } else {
                return 0; 
            }
        }







}
