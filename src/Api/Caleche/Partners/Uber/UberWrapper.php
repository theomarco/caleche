<?php
/**
 * Default controller for base endpoints.
 */

namespace Api\Caleche\Partners;

use Api\Application;
use GuzzleHttp\Client;

class UberWrapper 
{

    public function requestEstimate($lat, $lon){
        $r = $client->request('POST', 'http://httpbin.org//v1/requests/estimate', [
            'json' => ['foo' => 'bar']
        ]);
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
