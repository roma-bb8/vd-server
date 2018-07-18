<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 26.02.17
 * Time: 11:45
 */

namespace Acme\Routes;


use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class UserRoutes implements ControllerProviderInterface {

    /**
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app) {

        $routes = $app['controllers_factory'];

        $routes->post('/login', 'Acme\Controllers\UserCtrl::getUserAndToken');
        $routes->put('/update', 'Acme\Controllers\UserCtrl::updateYourAccount');

        return $routes;
    }
}
