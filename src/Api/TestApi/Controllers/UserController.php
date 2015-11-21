<?php
/**
 * Basic sample of a RESTful endpoint using static data.
 */
namespace Api\TestApi\Controllers;

use Api\Application;
use Api\Controller;

class UserController extends Controller
{

    public function all(Application $app)
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
