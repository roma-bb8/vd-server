<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 26.02.17
 * Time: 11:54
 */

namespace Acme\Routes;


use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class NurseRoutes implements ControllerProviderInterface {

    /**
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app) {

        $routes = $app['controllers_factory'];

        $routes->post('/patient', 'Acme\Controllers\PatientCtrl::create');
        $routes->put('/patient', 'Acme\Controllers\PatientCtrl::update');
        $routes->delete('/patient/{id}', 'Acme\Controllers\PatientCtrl::delete')->assert('id', '^[1-9]\d*$');
        $routes->get('/patient/{id}', 'Acme\Controllers\PatientCtrl::getByID')->assert('id', '^[1-9]\d*$');
        $routes->get('/patients', 'Acme\Controllers\PatientCtrl::getPatients');

        $routes->post('/reception', 'Acme\Controllers\ReceptionCtrl::create');
        $routes->put('/reception', 'Acme\Controllers\ReceptionCtrl::update');
        $routes->delete('/reception/{id}', 'Acme\Controllers\ReceptionCtrl::delete')->assert('id', '^[1-9]\d*$');
        $routes->get('/reception/{id}', 'Acme\Controllers\ReceptionCtrl::getByID')->assert('id', '^[1-9]\d*$');
        $routes->get('/receptions', 'Acme\Controllers\ReceptionCtrl::getReceptions');

        $routes->get('/doctors', 'Acme\Controllers\WorkerCtrl::getDoctors');
        $routes->get('/services', 'Acme\Controllers\ServiceCtrl::getServices');

        $routes->get('/professions', 'Acme\Controllers\ProfessionCtrl::getProfessions');

        $routes->get('/busy/time/{id}/{date}', 'Acme\Controllers\ReceptionCtrl::getBusyTime')->assert('id', '^[1-9]\d*$')->assert('date', '^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$');
        $routes->get('/reception/paid/{id}', 'Acme\Controllers\ReceptionCtrl::paidReception')->assert('id', '^[1-9]\d*$');

        return $routes;
    }
}
