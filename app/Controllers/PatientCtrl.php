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
use Acme\Exception\AcmeInvalidParameterException;
use Acme\Exception\AcmeNotFound;
use Acme\Models\Patient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PatientCtrl extends Controller {

    /**
     * @Route("/api/nurse/patient")
     * @Method("POST")
     *
     * @param Application $app
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Application $app, Request $request) {

        $data = $this->getDataFromRequest($request);

        $patient = new Patient();

        $patient->setFirst($data['first']);
        $patient->setSecond($data['second']);
        $patient->setLast($data['last']);
        $patient->setSex($data['sex']);
        $patient->setBirthday($data['birthday']);
        $patient->setAddress($data['address']);
        $patient->setPhone($data['phone']);
        $patient->setNote($data['note']);

        $this->checkObject($app, $patient);

        $app['db']->insert('patients', array(
            'first'            => $patient->getFirst(),
            'second'           => $patient->getSecond(),
            'last'             => $patient->getLast(),
            'sex'              => (int) $patient->isSex(),
            'birthday'         => $patient->getBirthday(),
            'phone'            => $patient->getPhone(),
            'address'          => $patient->getAddress(),
            'note'             => $patient->getNote(),
            'info_creating_id' => $app['user']->getId(),
            'info_changed_id'  => $app['user']->getId()
        ));

        return $app->json(array(
            'id'       => (int) $app['db']->lastInsertId(),
            'first'    => $patient->getFirst(),
            'second'   => $patient->getSecond(),
            'last'     => $patient->getLast(),
            'sex'      => $patient->isSex(),
            'birthday' => $patient->getBirthday(),
            'address'  => $patient->getAddress(),
            'phone'    => $patient->getPhone(),
            'note'     => $patient->getNote()
        ), Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/nurse/patient")
     * @Method("PUT")
     *
     * @param Application $app
     * @param Request $request
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function update(Application $app, Request $request) {

        $data = $this->getDataFromRequest($request);

        $patient = new Patient();

        $patient->setId($data['id']);
        $patient->setFirst($data['first']);
        $patient->setSecond($data['second']);
        $patient->setLast($data['last']);
        $patient->setSex($data['sex']);
        $patient->setBirthday($data['birthday']);
        $patient->setPhone($data['phone']);
        $patient->setAddress($data['address']);
        $patient->setNote($data['note']);

        $this->checkObject($app, $patient);

        $update = $app['db']->update('patients', array(
            'first'           => $patient->getFirst(),
            'second'          => $patient->getSecond(),
            'last'            => $patient->getLast(),
            'sex'             => (int) $patient->isSex(),
            'birthday'        => $patient->getBirthday(),
            'address'         => $patient->getAddress(),
            'phone'           => $patient->getPhone(),
            'note'            => $patient->getNote(),
            'info_changed_id' => $app['user']->getId()
        ), array('id' => $patient->getId()));

        if ($update === 0) throw new AcmeNotFound('patient_not_update');

        return $app->json(array(
            'id'       => $patient->getId(),
            'first'    => $patient->getFirst(),
            'second'   => $patient->getSecond(),
            'last'     => $patient->getLast(),
            'sex'      => $patient->isSex(),
            'birthday' => $patient->getBirthday(),
            'address'  => $patient->getAddress(),
            'phone'    => $patient->getPhone(),
            'note'     => $patient->getNote()
        ));
    }

    /**
     * @Route("/api/nurse/patient/{id}")
     * @Method("DELETE")
     *
     * @param Application $app
     * @param integer $id
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function delete(Application $app, $id) {

        if ($app['db']->delete('patients', array('id' => $id))) return $app->json(null, Response::HTTP_NO_CONTENT);

        throw new AcmeNotFound('empty_patient_id', $id);
    }

    /**
     * @Route("/api/nurse/patient/{id}")
     * @Method("GET")
     *
     * @param Application $app
     * @param integer $id
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function getByID(Application $app, $id) {

        $patient = $app['db']->fetchAssoc('
          SELECT
            *
          FROM
            patients
          WHERE
            id IN (:id)
        ', array('id' => $id));

        if (empty($patient)) throw new AcmeNotFound('empty_patient_id', $id);

        return $app->json(array(
            'id'       => (int) $patient['id'],
            'first'    => $patient['first'],
            'second'   => $patient['second'],
            'last'     => $patient['last'],
            'sex'      => (bool) $patient['sex'],
            'birthday' => $patient['birthday'],
            'address'  => $patient['address'],
            'phone'    => $patient['phone'],
            'note'     => $patient['note']
        ));
    }

    /**
     * @Route("/api/nurse/patients")
     * @Method("GET")
     *
     * @param Application $app
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function getPatients(Application $app) {

        $patients = $app['db']->fetchAll('
          SELECT
            id,
            CONCAT(last, \' \',  first, \' \', second) AS full_name, 
            phone
          FROM
            patients
        ');

        if (empty($patients)) throw new AcmeNotFound('empty_patients');

        for ($i = 0; $i < count($patients); $i++) settype($patients[$i]['id'], 'int');

        return $app->json($patients);
    }

    /**
     * @Route("/api/doctor/patient/note")
     * @Method("PUT")
     *
     * @param Application $app
     * @param Request $request
     * @return JsonResponse
     * @throws AcmeInvalidParameterException
     * @throws AcmeNotFound
     */
    public function updatePatientNote(Application $app, Request $request) {

        $data = $this->getDataFromRequest($request);

        if (empty($data['note'])) throw new AcmeInvalidParameterException('note', Model::EMPTY_MESSAGE);

        $patient = new Patient();

        $patient->setId($data['id']);
        $patient->setNote($data['note']);

        $this->checkObject($app, $patient);

        $update = $app['db']->update('patients', array(
            'note'            => $patient->getNote(),
            'info_changed_id' => $app['user']->getId()
        ), array('id' => $patient->getId()));

        if ($update === 0) throw new AcmeNotFound('patient_not_update');

        return $app->json(array(
            'id'   => $patient->getId(),
            'note' => $patient->getNote()
        ));
    }
}
