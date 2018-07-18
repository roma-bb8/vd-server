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

class DoctorRoutes implements ControllerProviderInterface {

    /**
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app) {

        $routes = $app['controllers_factory'];

        $routes->get('/receptions', 'Acme\Controllers\ReceptionCtrl::getReceptionsDoctor');
        $routes->get('/reception/{id}', 'Acme\Controllers\ReceptionCtrl::getByID')->assert('id', '^[1-9]\d*$');
        $routes->put('/reception/end/{id}', 'Acme\Controllers\ReceptionCtrl::end')->assert('id', '^[1-9]\d*$');
        $routes->get('/receptions/history/patient/{id}', 'Acme\Controllers\ReceptionCtrl::getReceptionsHistoryPatientByID')->assert('id', '^[1-9]\d*$');

        $routes->get('/patient/{id}', 'Acme\Controllers\PatientCtrl::getByID')->assert('id', '^[1-9]\d*$');

        $routes->post('/reception/protocol', 'Acme\Controllers\ReceptionCtrl::addProtocolToReception');
        $routes->delete('/reception/protocol/{reception_id}/{patient_id}', 'Acme\Controllers\ReceptionCtrl::deleteProtocolFromReception')->assert('reception_id', '^[1-9]\d*$')->assert('patient_id', '^[1-9]\d*$');
        $routes->get('/reception/protocol/{reception_id}/{patient_id}', 'Acme\Controllers\ReceptionCtrl::getProtocolFromReception')->assert('reception_id', '^[1-9]\d*$')->assert('patient_id', '^[1-9]\d*$');

        $routes->post('/reception/analyzes', 'Acme\Controllers\ReceptionCtrl::addAnalysisToReception');
        $routes->delete('/reception/analyzes/{id}', 'Acme\Controllers\ReceptionCtrl::deleteAnalysisFromReception')->assert('id', '^[1-9]\d*$');

        $routes->put('/patient/note', 'Acme\Controllers\PatientCtrl::updatePatientNote');

        $routes->get('/protocol/list', 'Acme\Controllers\WorkerCtrl::getProtocolList');

        return $routes;
    }
}
