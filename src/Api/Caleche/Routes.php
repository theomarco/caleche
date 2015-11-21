<?php

namespace Api\TestApi;

class Routes extends \Api\Routes
{
    /**
     * Register example endpoints here.
     */
    public function register()
    {
        $this->getApp()->get('/test/users',         'Api\TestApi\Controllers\UserController::all');
        $this->getApp()->get('/test/users/{id}',    'Api\TestApi\Controllers\UserController::get');
        $this->getApp()->put('/test/users/{id}',    'Api\TestApi\Controllers\UserController::put');
        $this->getApp()->delete('/test/users/{id}', 'Api\TestApi\Controllers\UserController::delete');
    }

}
