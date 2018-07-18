<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 26.02.17
 * Time: 11:54
 */

namespace Acme\Routes;


use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class AdminRoutes implements ControllerProviderInterface {

    /**
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app) {

        $routes = $app['controllers_factory'];

        $routes->post('/profession', 'Acme\Controllers\ProfessionCtrl::create');
        $routes->put('/profession', 'Acme\Controllers\ProfessionCtrl::update');
        $routes->delete('/profession/{id}', 'Acme\Controllers\ProfessionCtrl::delete')->assert('id', '^[1-9]\d*$');
        $routes->get('/profession/{id}', 'Acme\Controllers\ProfessionCtrl::getByID')->assert('id', '^[1-9]\d*$');
        $routes->get('/professions', 'Acme\Controllers\ProfessionCtrl::getProfessions');
        $routes->put('/profession/binding', 'Acme\Controllers\ProfessionCtrl::setDependencies');
        $routes->get('/profession/binding', 'Acme\Controllers\ProfessionCtrl::getDependencies');

        $routes->post('/worker', 'Acme\Controllers\WorkerCtrl::create');
        $routes->put('/worker', 'Acme\Controllers\WorkerCtrl::update');
        $routes->patch('/worker/{id}', 'Acme\Controllers\WorkerCtrl::restorePassword')->assert('id', '^[1-9]\d*$');
        $routes->delete('/worker/{id}', 'Acme\Controllers\WorkerCtrl::delete')->assert('id', '^[1-9]\d*$');
        $routes->get('/worker/{id}', 'Acme\Controllers\WorkerCtrl::getByID')->assert('id', '^[1-9]\d*$');
        $routes->get('/workers', 'Acme\Controllers\WorkerCtrl::getWorkers');

        $routes->put('/protocol', 'Acme\Controllers\ProtocolCtrl::update');
        $routes->get('/protocol/{id}', 'Acme\Controllers\ProtocolCtrl::getByID')->assert('id', '^[1-9]\d*$');
        $routes->get('/protocols', 'Acme\Controllers\ProtocolCtrl::getProtocols');

        $routes->post('/service', 'Acme\Controllers\ServiceCtrl::create');
        $routes->put('/service', 'Acme\Controllers\ServiceCtrl::update');
        $routes->delete('/service/{id}', 'Acme\Controllers\ServiceCtrl::delete')->assert('id', '^[1-9]\d*$');
        $routes->get('/service/{id}', 'Acme\Controllers\ServiceCtrl::getByID')->assert('id', '^[1-9]\d*$');
        $routes->get('/services', 'Acme\Controllers\ServiceCtrl::getServices');

        return $routes;
    }
}
