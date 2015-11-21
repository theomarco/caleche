<?php
/**
 * Basic sample of a RESTful endpoint using static data.
 */
namespace Api\Caleche\Controllers;

use Api\Application;
use Api\Controller;
use Api\Caleche\Partners\Uber\Uber;

class CalecheController extends Controller
{

    public function request(Application $app)
    {
        $users = array();

        for ($i=1;$i<=10;$i++) {
            $users[] = array('id' => $i, 'name' => 'gumby'.$i);
        }

        return $app->json($users);
    }

    public function get(Application $app, $id)
    {
        return $app->json(array('id' => (int)$id, 'name' => 'gumby'.$id));
    }

}
