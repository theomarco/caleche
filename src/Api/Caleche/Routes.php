<?php

namespace Api\Caleche;

class Routes extends \Api\Routes
{
    /**
     * Register example endpoints here.
     */
    public function register()
    {
        // $this->getApp()->get('/test/users',         'Api\TestApi\Controllers\UserController::all');
        // $this->getApp()->get('/test/users/{id}',    'Api\TestApi\Controllers\UserController::get');
        // $this->getApp()->put('/test/users/{id}',    'Api\TestApi\Controllers\UserController::put');
        // $this->getApp()->delete('/test/users/{id}', 'Api\TestApi\Controllers\UserController::delete');
        $this->getApp()->post('/v1/request', 'Api\Caleche\Controllers\CalecheController::requestAll');
        $this->getApp()->post('/v1/cheapest', 'Api\Caleche\Controllers\CalecheController::cheapest');
        $this->getApp()->post('/v1/closest', 'Api\Caleche\Controllers\CalecheController::closest');

    }

}
