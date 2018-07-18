<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 15.01.17
 * Time: 13:37
 */

namespace Acme\Controllers;


use Acme\Api\Application;
use Acme\Api\Controller;
use Acme\Api\Model;
use Acme\Exception\AcmeDBALException;
use Acme\Exception\AcmeException;
use Acme\Exception\AcmeImageException;
use Acme\Exception\AcmeInvalidParameterException;
use Acme\Exception\AcmeNotFound;
use Acme\Models\Reception;
use Doctrine\DBAL\Connection;
use RedBeanPHP\Facade as R;
use RedBeanPHP\OODBBean;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReceptionCtrl extends Controller {

    /**
     * @Route("/api/nurse/reception")
     * @Method("POST")
     *
     * @param Application $app
     * @param Request $request
     * @return JsonResponse
     * @throws AcmeInvalidParameterException
     * @throws AcmeException
     * @throws AcmeDBALException
     */
    public function create(Application $app, Request $request) {

        $data = $this->getDataFromRequest($request);

        /** @var Connection $conn */
        $conn = $app['db'];

        $data = $this->getDoctorAndPatientAndServices($conn, $data);

        $reception = new Reception();

        $reception->getPatient()->setId($data['patient'] ['id']);
        $reception->getWorker()->setId($data['doctor']['id']);
        $reception->setTime($data['time']);

        $this->checkObject($app, $reception);
        $time = $this->isFreeTime($conn, $reception);

        if ($time !== true) throw new AcmeException($app->trans('busy_time_reception', array('%time%' => $time)));

        $conn->insert('receptions', array(
            'patient_id'       => $reception->getPatient()->getId(),
            'worker_id'        => $reception->getWorker()->getId(),
            'time'             => $reception->getTime(),
            'info_creating_id' => $app['user']->getId(),
            'info_changed_id'  => $app['user']->getId()
        ));

        $reception->setId($conn->lastInsertId());

        foreach ($data['services'] as $service) {
            $conn->insert('receptions_services', array(
                'reception_id'     => $reception->getId(),
                'service_id'       => $service['id'],
                'info_creating_id' => $app['user']->getId(),
                'info_changed_id'  => $app['user']->getId()
            ));
        }

        $reception = $conn->fetchAssoc('
          SELECT
            receptions.id AS reception_id,
            receptions.time AS reception_time,
            receptions.active AS reception_active,
            receptions.paid AS reception_paid,
            
            receptions.protocol_sample_id AS reception_sample,
            receptions.protocol_template_id AS reception_template,
            receptions.analyzes AS reception_analyzes,
            
            receptions.patient_id AS patient_id,
            CONCAT(patients.last, \' \',  patients.first, \' \', patients.second) AS patient_full_name, 
            
            receptions.worker_id AS worker_id,
            CONCAT(workers.last, \' \',  workers.first, \' \', workers.second) AS worker_full_name
          FROM
            `receptions`
          JOIN
            `patients`
          ON
            receptions.patient_id = patients.id
          JOIN
            `workers`
          ON
            receptions.worker_id = workers.id
          WHERE
            receptions.id IN (:id)
        ', array('id' => $reception->getId()));

        return $app->json(array(
            'id'                => (int) $reception['reception_id'],
            'time'              => $reception['reception_time'],
            'active'            => (bool) $reception['reception_active'],
            'paid'              => (bool) $reception['reception_paid'],
            'content'           => array(
                'primary_survey_id' => (int) $reception['reception_sample'],
                'diagnosis_id'      => (int) $reception['reception_template'],
                'analyzes'          => array_filter(explode(',', $reception['reception_analyzes']))
            ),
            'patient_id'        => (int) $reception['patient_id'],
            'patient_full_name' => $reception['patient_full_name'],
            'worker_id'         => (int) $reception['worker_id'],
            'worker_full_name'  => $reception['worker_full_name'],
        ), Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/nurse/reception")
     * @Method("PUT")
     *
     * @param Application $app
     * @param Request $request
     * @return JsonResponse
     * @throws AcmeInvalidParameterException
     * @throws AcmeException
     * @throws AcmeNotFound
     * @throws AcmeDBALException
     */
    public function update(Application $app, Request $request) {

        $data = $this->getDataFromRequest($request);

        /** @var Connection $conn */
        $conn = $app['db'];

        $data = $this->getDoctorAndPatientAndServices($conn, $data);

        $reception = new Reception();

        $reception->setId($data['id']);
        $reception->getPatient()->setId($data['patient'] ['id']);
        $reception->getWorker()->setId($data['doctor']['id']);
        $reception->setTime($data['time']);
        $reception->setPaid($data['paid']);

        $this->checkObject($app, $reception);
        $time = $this->isFreeTime($conn, $reception, $reception->getId());

        if ($time !== true) throw new AcmeException($app->trans('busy_time_reception', array('%time%' => $time)));

        $update = $app['db']->update('receptions', array(
            'patient_id'      => $reception->getPatient()->getId(),
            'worker_id'       => $reception->getWorker()->getId(),
            'time'            => $reception->getTime(),
            'paid'            => (int) $reception->isPaid(),
            'info_changed_id' => $app['user']->getId()
        ), array('id' => $reception->getId()));

        if ($update === 0) throw new AcmeNotFound('empty_reception_id', $reception->getId());

        $app['db']->delete('receptions_services', array('reception_id' => $data['id']));

        foreach ($data['services'] as $service) {
            $app['db']->insert('receptions_services', array(
                'reception_id'     => $reception->getId(),
                'service_id'       => $service['id'],
                'info_creating_id' => $app['user']->getId(),
                'info_changed_id'  => $app['user']->getId()
            ));
        }

        $reception = $app['db']->fetchAssoc('
          SELECT
            receptions.id AS reception_id,
            receptions.time AS reception_time,
            receptions.active AS reception_active,
            receptions.paid AS reception_paid,
            
            receptions.protocol_sample_id AS reception_sample,
            receptions.protocol_template_id AS reception_template,
            receptions.analyzes AS reception_analyzes,
            
            receptions.patient_id AS patient_id,
            CONCAT(patients.last, \' \',  patients.first, \' \', patients.second) AS patient_full_name, 
            
            receptions.worker_id AS worker_id,
            CONCAT(workers.last, \' \',  workers.first, \' \', workers.second) AS worker_full_name
          FROM
            `receptions`
          JOIN
            `patients`
          ON
            receptions.patient_id = patients.id
          JOIN
            `workers`
          ON
            receptions.worker_id = workers.id
          WHERE
            receptions.id IN (:id)
        ', array('id' => $reception->getId()));

        return $app->json(array(
            'id'                => (int) $reception['reception_id'],
            'time'              => $reception['reception_time'],
            'active'            => (bool) $reception['reception_active'],
            'paid'              => (bool) $reception['reception_paid'],
            'content'           => array(
                'primary_survey_id' => (int) $reception['reception_sample'],
                'diagnosis_id'      => (int) $reception['reception_template'],
                'analyzes'          => array_filter(explode(',', $reception['reception_analyzes']))
            ),
            'patient_id'        => (int) $reception['patient_id'],
            'patient_full_name' => $reception['patient_full_name'],
            'worker_id'         => (int) $reception['worker_id'],
            'worker_full_name'  => $reception['worker_full_name'],
        ));
    }

    /**
     * @Route("/api/nurse/reception")
     * @Method("PUT")
     *
     * @param Application $app
     * @param integer $id
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function end(Application $app, $id) {

        if ($app['db']->update('receptions', array('active' => 0), array('id' => $id)) > 0) {
            return $app->json(null, Response::HTTP_NO_CONTENT);
        }

        throw new AcmeNotFound('empty_reception_id', $id);
    }

    /**
     * @Route("/api/nurse/reception")
     * @Method("DELETE")
     *
     * @param Application $app
     * @param integer $id
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function delete(Application $app, $id) {

        $app['db']->delete('receptions_services', array('reception_id' => $id));

        if ($app['db']->delete('receptions', array('id' => $id))) return $app->json(null, Response::HTTP_NO_CONTENT);

        throw new AcmeNotFound('empty_reception_id', $id);
    }

    /**
     * @Route("/api/doctor/reception/{id}")
     * @Route("/api/nurse/reception/{id}")
     * @Method("GET")
     *
     * @param Application $app
     * @param integer $id
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function getByID(Application $app, $id) {

        $reception = $app['db']->fetchAssoc('
          SELECT
            receptions.id AS reception_id,
            receptions.time AS reception_time,
            receptions.active AS reception_active,
            receptions.paid AS reception_paid,
            
            receptions.protocol_sample_id AS reception_sample,
            receptions.protocol_template_id AS reception_template,
            receptions.analyzes AS reception_analyzes,
            
            receptions.patient_id AS patient_id,
            CONCAT(patients.last, \' \',  patients.first, \' \', patients.second) AS patient_full_name, 
            
            receptions.worker_id AS worker_id,
            CONCAT(workers.last, \' \',  workers.first, \' \', workers.second) AS worker_full_name
          FROM
            `receptions`
          JOIN
            `patients`
          ON
            receptions.patient_id = patients.id
          JOIN
            `workers`
          ON
            receptions.worker_id = workers.id
          WHERE
            receptions.id IN (:id)
        ', array('id' => $id));

        if (empty($reception)) throw new AcmeNotFound('empty_reception_id', $id);

        $services = $app['db']->fetchAll('
          SELECT
            services.id
          FROM
            `services`
          JOIN
            `receptions_services`
          ON
            services.id = receptions_services.service_id
          WHERE
            receptions_services.reception_id = :reception_id
        ', array('reception_id' => $reception['reception_id']));

        return $app->json(array(
            'id'                => (int) $reception['reception_id'],
            'time'              => $reception['reception_time'],
            'active'            => (bool) $reception['reception_active'],
            'paid'              => (bool) $reception['reception_paid'],
            'service_ids'       => array_map(function ($data) {
                return intval($data['id']);
            }, $services),
            'content'           => array(
                'primary_survey_id' => (int) $reception['reception_sample'],
                'diagnosis_id'      => (int) $reception['reception_template'],
                'analyzes'          => array_filter(explode(',', $reception['reception_analyzes']))
            ),
            'patient_id'        => (int) $reception['patient_id'],
            'patient_full_name' => $reception['patient_full_name'],
            'worker_id'         => (int) $reception['worker_id'],
            'worker_full_name'  => $reception['worker_full_name']
        ));
    }

    /**
     * @Route("/api/doctor/receptions")
     * @Route("/api/nurse/receptions")
     * @Method("GET")
     *
     * @param Application $app
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function getReceptions(Application $app) {

        $receptions = $app['db']->fetchAll('
          SELECT
            receptions.id AS id,
            receptions.time AS time,
            CONCAT(patients.last, \' \',  patients.first, \' \', patients.second) AS patient_full_name, 
            receptions.worker_id AS worker_id,
            CONCAT(workers.last, \' \',  workers.first, \' \', workers.second) AS worker_full_name,
            (SELECT 
                SUM(services.price)
              FROM 
                `services`
              JOIN 
                `receptions_services`
              ON
                services.id = receptions_services.service_id
              WHERE
                receptions_services.reception_id = receptions.id
            ) AS price
          FROM
            `receptions`
          JOIN
            `patients`
          ON
            receptions.patient_id = patients.id
          JOIN
            `workers`
          ON
            receptions.worker_id = workers.id
          WHERE
            receptions.active = TRUE
        ');

        if (empty($receptions)) throw new AcmeNotFound('empty_receptions');

        for ($i = 0; $i < count($receptions); $i++) {

            settype($receptions[$i]['id'], 'int');
            settype($receptions[$i]['worker_id'], 'int');
            $receptions[$i]['price'] = round($receptions[$i]['price'], 2);
        }

        return $app->json($receptions);
    }

    /**
     * @Route("/api/doctor/receptions")
     * @Method("GET")
     *
     * @param Application $app
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function getReceptionsDoctor(Application $app) {

        $receptions = $app['db']->fetchAll('
          SELECT
            receptions.id AS id,
            receptions.time AS time,
            patients.id AS patient_id,
            CONCAT(patients.last, \' \',  patients.first, \' \', patients.second) AS patient_full_name
          FROM
            `receptions`
          JOIN
            `patients`
          ON
            receptions.patient_id = patients.id
          JOIN
            `workers`
          ON
            receptions.worker_id = workers.id
          WHERE
            receptions.active = TRUE
          AND
            receptions.worker_id = :id
          ORDER BY
            receptions.time ASC 
        ', array('id' => $app['user']->getId()));

        if (empty($receptions)) throw new AcmeNotFound('empty_receptions');

        for ($i = 0; $i < count($receptions); $i++) {

            settype($receptions[$i]['id'], 'int');
            settype($receptions[$i]['patient_id'], 'int');
        }

        return $app->json($receptions);
    }

    /**
     * @Route("/api/doctor/receptions/history/patient/{id}")
     * @Method("GET")
     *
     * @param Application $app
     * @param $id
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function getReceptionsHistoryPatientByID(Application $app, $id) {

        $receptions = $app['db']->fetchAll('
          SELECT
            receptions.id AS id,
            receptions.time AS time,
            workers.id AS worker_id,
            CONCAT(workers.last, \' \',  workers.first, \' \', workers.second) AS worker_full_name
          FROM
            `receptions`
          JOIN
            `workers`
          ON
            receptions.worker_id = workers.id
          WHERE
            receptions.patient_id = :id
          ORDER BY
            receptions.time ASC
        ', array('id' => $id));

        if (empty($receptions)) throw new AcmeNotFound('empty_reception_id', $id);

        for ($i = 0; $i < count($receptions); $i++) {

            settype($receptions[$i]['id'], 'int');
            settype($receptions[$i]['worker_id'], 'int');
        }

        return $app->json($receptions);
    }


    /**
     * @Route("/api/doctor/reception/protocol")
     * @Method("GET")
     *
     * @param Application $app
     * @param Request $request
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function addProtocolToReception(Application $app, Request $request) {

        $data = $this->getDataFromRequest($request);

        \Acme\Api\R::init($app);

        $worker = R::load('workers', $app['user']->getId());

        $type = R::load('protocols', $data['protocol_id']);
        if ($type->getID() == 0) throw new AcmeNotFound('empty_protocol_id', $data['protocol_id']);

        $reception = R::load('receptions', $data['reception_id']);
        if ($reception->getID() == 0) throw new AcmeNotFound('empty_reception_id', $data['reception_id']);

        unset($data['protocol_id'], $data['reception_id']);

        /** @var OODBBean $protocol */
        $protocol = R::dispense($type->table);

        $protocol->import($data);
        $protocol->reception = $reception;
        $protocol->info_creating_id = $worker;
        $protocol->info_changed_id = $worker;
        $protocol->info_create_time = R::isoDateTime();
        $protocol->info_changed_time = R::isoDateTime();

        $id = R::store($protocol);

        if ($type->type === 'TYPE_SAMPLE') {
            $app['db']->update('receptions', array('protocol_sample_id' => $type->getID()), array('id' => $reception->getID()));
        } else {
            $app['db']->update('receptions', array('protocol_template_id' => $type->getID()), array('id' => $reception->getID()));
        }

        $protocol = R::load($type->table, $id)->export();

        settype($protocol['id'], 'int');
        settype($protocol['reception_id'], 'int');
        unset($protocol['info_create_time'], $protocol['info_changed_time'], $protocol['info_creating_id_id'], $protocol['info_changed_id_id']);

        return $app->json($protocol);
    }

    /**
     * @Route("/api/doctor/reception/protocol/{id}/{id}")
     * @Method("GET")
     *
     * @param Application $app
     * @param integer $reception_id
     * @param integer $patient_id
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function getProtocolFromReception(Application $app, $reception_id, $patient_id) {

        $table = $app['db']->fetchAssoc('
          SELECT
            protocols.table
          FROM
            `receptions`
          JOIN 
            `protocols`
          ON 
            protocols.id = :protocolId
          WHERE
            receptions.id = :receptionId
          AND
            protocols.id = receptions.protocol_sample_id
          OR 
            protocols.id = receptions.protocol_template_id
        ', array(
            'protocolId'  => $patient_id,
            'receptionId' => $reception_id
        ));

        if (empty($table)) throw new AcmeNotFound('no_protocol');

        \Acme\Api\R::init($app);

        $protocol = R::findOne($table['table'], 'reception_id = ?', array($reception_id))->export();

        settype($protocol['id'], 'int');
        settype($protocol['reception_id'], 'int');
        unset($protocol['info_create_time'], $protocol['info_changed_time'], $protocol['info_creating_id_id'], $protocol['info_changed_id_id']);

        return $app->json($protocol);
    }

    /**
     * @Route("/api/doctor/reception/protocol/{id}/{id}")
     * @Method("DELETE")
     *
     * @param Application $app
     * @param $reception_id
     * @param $patient_id
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function deleteProtocolFromReception(Application $app, $reception_id, $patient_id) {

        $type = $app['db']->fetchAssoc('
          SELECT
            protocols.table, protocols.type
          FROM
            `receptions`
          JOIN 
            `protocols`
          ON 
            protocols.id = :protocolId
          WHERE
            receptions.id = :receptionId
          AND
            protocols.id = receptions.protocol_sample_id
          OR 
            protocols.id = receptions.protocol_template_id
          LIMIT 1
        ', array(
            'protocolId'  => $patient_id,
            'receptionId' => $reception_id
        ));

        if (empty($type)) throw new AcmeNotFound('no_protocol');

        \Acme\Api\R::init($app);

        $protocol = R::findOne($type['table'], 'reception_id = ?', array($reception_id));

        R::trash($protocol);

        if ($type['type'] === 'TYPE_SAMPLE') {
            $app['db']->update('receptions', array('protocol_sample_id' => null), array('id' => $reception_id));
        } else {
            $app['db']->update('receptions', array('protocol_template_id' => null), array('id' => $reception_id));
        }

        return $app->json(null, Response::HTTP_NO_CONTENT);
    }


    /**
     * @Route("/api/doctor/reception/analyzes")
     * @Method("POST")
     *
     * @param Application $app
     * @param Request $request
     * @return Response
     * @throws AcmeException
     * @throws AcmeImageException
     * @throws AcmeInvalidParameterException
     * @throws AcmeNotFound
     */
    public function addAnalysisToReception(Application $app, Request $request) {

        /** @var Connection $conn */
        $conn = $app['db'];

        $reception = $conn->fetchAssoc('SELECT id, analyzes FROM receptions WHERE id IN (?)', array($request->get('id')));

        if (empty($reception)) throw new AcmeNotFound('empty_reception_id', $request->get('id'));
        if (empty($request->files)) throw new AcmeInvalidParameterException('images', Model::EMPTY_MESSAGE);

        foreach ($request->files as $image) {

            /** @var UploadedFile $image */
            if ($image->getError() !== UPLOAD_ERR_OK) {
                throw new AcmeImageException($image->getError(), $image->getClientOriginalName());
            } elseif ($image->getMimeType() === 'image/gif') continue;
            elseif ($image->getMimeType() === 'image/jpeg') continue;
            elseif ($image->getMimeType() === 'image/pjpeg') continue;
            elseif ($image->getMimeType() === 'image/png') continue;
            else throw new AcmeImageException(5, $image->getClientOriginalName());
        }

        $dir = date('Y-m');
        $path = __DIR__ . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, array('..', '..', 'web', 'images', $dir));

        if (!file_exists($path)) {
            if (!mkdir($path, 0755, true)) {
                throw new AcmeException($app->trans('not_create_folder'), Response::HTTP_INSUFFICIENT_STORAGE);
            }
        }

        $analyzes = array();

        foreach ($request->files as $image) {

            $name = md5(uniqid()) . '.' . $image->guessExtension();
            $image->move($path, $name);

            $analyzes[] = '/' . $dir . '/' . $name;
        }

        $path = __DIR__ . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, array('..', '..', 'web', 'images'));

        foreach (array_filter(explode(',', $reception['analyzes'])) as $analyze) {

            $image = $path . $analyze;

            if (!file_exists($image)) continue;

            unlink($image);
        }

        if ($reception['analyzes'] !== null) {
            $analyzes = array_merge($analyzes, explode(',', $reception['analyzes']));
        }

        $conn->update('receptions', array(
            'analyzes' => implode(',', $analyzes)
        ), array('id' => $reception['id']));

        return new Response(null, Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/doctor/reception/analyzes/{id}")
     * @Method("DELETE")
     *
     * @param Application $app
     * @param integer $id
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function deleteAnalysisFromReception(Application $app, $id) {

        $reception = $app['db']->fetchAssoc('
          SELECT
            `id`, `analyzes`
          FROM
            `receptions`
          WHERE
            `id` IN (:id)
        ', array('id' => $id));

        if (empty($reception)) throw new AcmeNotFound('empty_reception_id', $id);

        $path = __DIR__ . '/../../web/images';
        $dir = '';

        foreach (array_filter(explode(',', $reception['analyzes'])) as $analyze) {

            $dir = dirname($analyze);

            $image = $path . $analyze;

            if (!file_exists($image)) continue;

            unlink($image);
        }

        if (count(glob($path . $dir . '/*')) === 0) rmdir($path . $dir);

        $update = $app['db']->update('receptions', array('analyzes' => null), array('id' => $reception['id']));

        if ($update === 0) throw new AcmeNotFound('empty_reception_id_images', $reception['id']);

        return $app->json(null, Response::HTTP_NO_CONTENT);
    }


    /**
     * @param Connection $conn
     * @param Reception $reception
     * @param int $id
     * @return bool
     */
    private function isFreeTime(Connection $conn, Reception $reception, $id = 0) {

        $isBusy = $conn->fetchAssoc('
          SELECT
            `time`
          FROM
            `receptions`
          WHERE
            `worker_id` = :worker_id
          AND
            `time` BETWEEN :time - INTERVAL 30 MINUTE AND :time + INTERVAL 30 MINUTE
          AND
            `id` NOT IN (:id)
        ', array(
            'id'        => $id,
            'worker_id' => $reception->getWorker()->getId(),
            'time'      => $reception->getTime()
        ));

        if (empty($isBusy)) return true;

        return $isBusy['time'];
    }

    /**
     * @param Connection $conn
     * @param array $data
     * @return array
     * @throws AcmeNotFound
     */
    private function getDoctorAndPatientAndServices(Connection $conn, $data) {

        $doctor = $conn->fetchAssoc('
          SELECT 
            id 
          FROM
            workers
          WHERE 
            id = :id 
          AND 
            status = TRUE 
          AND 
            roles
          LIKE 
            \'%ROLE_DOCTOR%\'
        ', array('id' => $data['worker_id']));

        if (empty($doctor)) throw new AcmeNotFound('empty_doctor_id', $data['worker_id']);

        $patient = $conn->fetchAssoc('
          SELECT 
            id 
          FROM 
            patients 
          WHERE 
            id = :id
        ', array('id' => $data['patient_id']));

        if (empty($patient)) throw new AcmeNotFound('empty_patient_id', $data['patient_id']);

        $sql = 'SELECT id FROM services WHERE id IN (?)';
        $query = $conn->executeQuery($sql, array($data['service_ids']), array(Connection::PARAM_INT_ARRAY));
        $services = $query->fetchAll();
        if (count($services) !== count($data['service_ids'])) throw new AcmeNotFound('empty_services');

        $data['doctor'] = $doctor;
        $data['patient'] = $patient;
        $data['services'] = $services;

        return $data;
    }

    /**
     * @Route("/api/nurse/busy/time/{id}/{date}")
     * @Method("GET")
     *
     * @param Application $app
     * @param integer $id
     * @param string $date
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function getBusyTime(Application $app, $id, $date) {

        $time = $app['db']->fetchAll('
          SELECT 
            TIME(receptions.time) AS time
          FROM
            `receptions`
          WHERE 
            receptions.worker_id = :id 
          AND 
            receptions.active = TRUE 
          AND
            DATE(receptions.time) = DATE(:date)
        ', array('id' => $id, 'date' => $date));

        if (empty($time)) throw new AcmeNotFound('empty_time_list', $date);

        return $app->json(array(
            'times' => array_map(function ($data) {
                return $data['time'];
            }, $time)
        ));
    }

    /**
     * @Route("/api/nurse/reception/paid/{id}")
     * @Method("GET")
     *
     * @param Application $app
     * @param integer $id
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function paidReception(Application $app, $id) {

        $reception = $app['db']->fetchAssoc('
          SELECT 
            receptions.id
          FROM
            `receptions`
          WHERE 
            receptions.id = :id
        ', array('id' => $id));

        if (empty($reception)) throw new AcmeNotFound('empty_reception_id', $id);

        $update = $app['db']->update('receptions', array(
            'paid'            => 1,
            'info_changed_id' => $app['user']->getId()
        ), array('id' => $id));

        if ($update === 0) throw new AcmeNotFound('empty_reception_id', $id);

        return $app->json(null, Response::HTTP_NO_CONTENT);
    }
}
