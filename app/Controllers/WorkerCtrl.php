<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 15.01.17
 * Time: 13:35
 */

namespace Acme\Controllers;


use Acme\Api\Application;
use Acme\Api\Controller;
use Acme\Exception\AcmeDBALException;
use Acme\Exception\AcmeException;
use Acme\Exception\AcmeInvalidParameterException;
use Acme\Exception\AcmeNotFound;
use Acme\Models\Worker;
use Acme\Provider\PasswordProvider;
use Doctrine\DBAL\Exception\DriverException;
use Swift_Message;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkerCtrl extends Controller {

    /**
     * @Route("/api/admin/worker")
     * @Method("POST")
     *
     * @param Application $app
     * @param Request $request
     * @return JsonResponse
     * @throws AcmeInvalidParameterException
     * @throws AcmeNotFound
     * @throws AcmeDBALException
     */
    public function create(Application $app, Request $request) {

        $data = $this->getDataFromRequest($request);

        $worker = new Worker();
        $passwordProvider = new PasswordProvider(array('length' => 10));

        $worker->setFirst($data['first']);
        $worker->setSecond($data['second']);
        $worker->setLast($data['last']);
        $worker->setSex($data['sex']);
        $worker->setBirthday($data['birthday']);
        $worker->setEmail($data['email']);
        $worker->setPhone($data['phone']);
        $worker->getProfession()->setId($data['profession_id']);
        $worker->setSalt(md5(uniqid()));
        $worker->setPassword($passwordProvider->getHashPassword($worker));
        $worker->setRoles($data['roles']);

        $this->checkObject($app, $worker);

        try {

            $app['db']->insert('workers', array(
                'first'            => $worker->getFirst(),
                'second'           => $worker->getSecond(),
                'last'             => $worker->getLast(),
                'sex'              => (int) $worker->isSex(),
                'birthday'         => $worker->getBirthday(),
                'email'            => $worker->getEmail(),
                'phone'            => $worker->getPhone(),
                'profession_id'    => $worker->getProfession()->getId(),
                'salt'             => $worker->getSalt(),
                'password'         => $passwordProvider->getHashPassword($worker),
                'roles'            => implode(',', $worker->getRoles()),
                'info_creating_id' => $app['user']->getId(),
                'info_changed_id'  => $app['user']->getId()
            ));

        } catch (DriverException $error) {

            if ($error->getErrorCode() === 1062) {
                throw new AcmeInvalidParameterException('phone', 'duplicate_phone');
            } elseif ($error->getErrorCode() === 1452) {
                throw new AcmeNotFound('empty_profession_id', $worker->getProfession()->getId());
            } else {
                throw new AcmeDBALException($app, $error->getErrorCode(), $error->getMessage());
            }
        }

        $this->sendUserLetter($app, $worker, $passwordProvider->getPassword());

        return $app->json(array(
            'id'         => (int) $app['db']->lastInsertId(),
            'first'      => $worker->getFirst(),
            'second'     => $worker->getSecond(),
            'last'       => $worker->getLast(),
            'sex'        => $worker->isSex(),
            'birthday'   => $worker->getBirthday(),
            'phone'      => $worker->getPhone(),
            'email'      => $worker->getEmail(),
            'roles'      => $worker->getRoles(),
            'profession' => array(
                'id' => $worker->getProfession()->getId()
            )
        ), Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/admin/worker")
     * @Method("PUT")
     *
     * @param Application $app
     * @param Request $request
     * @return JsonResponse
     * @throws AcmeInvalidParameterException
     * @throws AcmeNotFound
     * @throws AcmeDBALException
     */
    public function update(Application $app, Request $request) {

        $data = $this->getDataFromRequest($request);

        $worker = new Worker();

        $worker->setId($data['id']);
        $worker->setFirst($data['first']);
        $worker->setSecond($data['second']);
        $worker->setLast($data['last']);
        $worker->setSex($data['sex']);
        $worker->setBirthday($data['birthday']);
        $worker->setEmail($data['email']);
        $worker->setPhone($data['phone']);
        $worker->getProfession()->setId($data['profession_id']);
        $worker->setStatus($data['status']);
        $worker->setRoles($data['roles']);

        $this->checkObject($app, $worker);

        try {

            $update = $app['db']->update('workers', array(
                'first'           => $worker->getFirst(),
                'second'          => $worker->getSecond(),
                'last'            => $worker->getLast(),
                'sex'             => (int) $worker->isSex(),
                'birthday'        => $worker->getBirthday(),
                'phone'           => $worker->getPhone(),
                'email'           => $worker->getEmail(),
                'roles'           => implode(',', $worker->getRoles()),
                'status'          => (int) $worker->isStatus(),
                'profession_id'   => $worker->getProfession()->getId(),
                'info_changed_id' => $app['user']->getId()
            ), array('id' => $worker->getId()));

            if ($update === 0) throw new AcmeNotFound('empty_worker_id', $worker->getId());

        } catch (DriverException $error) {

            if ($error->getErrorCode() === 1062) {
                throw new AcmeInvalidParameterException('phone', 'duplicate_phone');
            } elseif ($error->getErrorCode() === 1452) {
                throw new AcmeNotFound('empty_profession_id', $worker->getProfession()->getId());
            } else {
                throw new AcmeDBALException($app, $error->getErrorCode(), $error->getMessage());
            }
        }

        return $app->json(array(
            'id'         => $worker->getId(),
            'first'      => $worker->getFirst(),
            'second'     => $worker->getSecond(),
            'last'       => $worker->getLast(),
            'sex'        => $worker->isSex(),
            'birthday'   => $worker->getBirthday(),
            'email'      => $worker->getEmail(),
            'phone'      => $worker->getPhone(),
            'profession' => array(
                'id' => $worker->getProfession()->getId()
            ),
            'status'     => $worker->isStatus(),
            'roles'      => $worker->getRoles()
        ));
    }

    /**
     * @Route("/api/admin/worker/{id}")
     * @Method("PATCH")
     *
     * @param Application $app
     * @param integer $id
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function restorePassword(Application $app, $id) {

        $user = $app['db']->fetchAssoc('
          SELECT
            first, second, sex, phone, email
          FROM
            workers
          WHERE
            id IN (:id)
        ', array('id' => $id));

        if (empty($user)) throw new AcmeNotFound('empty_worker_id', $id);

        $worker = new Worker();
        $passwordProvider = new PasswordProvider(array('length' => 10));

        $worker->setId($id);
        $worker->setFirst($user['first']);
        $worker->setSecond($user['second']);
        $worker->setEmail($user['email']);
        $worker->setSex((bool) $user['sex']);
        $worker->setPhone($user['phone']);
        $worker->setSalt(md5(uniqid()));
        $worker->setPassword($passwordProvider->getHashPassword($worker));

        $app['db']->update('workers', array(
            'salt'     => $worker->getSalt(),
            'password' => $worker->getPassword()
        ), array('id' => $id));

        $this->sendUserLetter($app, $worker, $passwordProvider->getPassword());

        return $app->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/api/admin/worker/{id}")
     * @Method("DELETE")
     *
     * @param Application $app
     * @param integer $id
     * @return JsonResponse
     * @throws AcmeException
     * @throws AcmeNotFound
     */
    public function delete(Application $app, $id) {

        if ($id < 2) throw new AcmeException($app->trans('worker_not_delete_root'), Response::HTTP_FORBIDDEN);

        if ($app['db']->delete('workers', array('id' => $id))) return $app->json(null, Response::HTTP_NO_CONTENT);

        throw new AcmeNotFound('empty_worker_id', $id);
    }

    /**
     * @Route("/api/admin/worker/{id}")
     * @Method("GET")
     *
     * @param Application $app
     * @param integer $id
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function getByID(Application $app, $id) {

        $worker = $app['db']->fetchAssoc('
          SELECT
            *, professions.id AS profession_ID, professions.name AS profession_NAME
          FROM
            `workers`
          JOIN
            `professions`
          ON
            professions.id = workers.profession_id
          WHERE
            workers.id IN (:id)
        ', array('id' => $id));

        if (empty($worker)) throw new AcmeNotFound('empty_worker_id', $id);

        return $app->json(array(
            'id'         => (int) $worker['id'],
            'first'      => $worker['first'],
            'second'     => $worker['second'],
            'last'       => $worker['last'],
            'sex'        => (bool) $worker['sex'],
            'birthday'   => $worker['birthday'],
            'phone'      => $worker['phone'],
            'email'      => $worker['email'],
            'profession' => array(
                'id'   => (int) $worker['profession_ID'],
                'name' => $worker['profession_NAME']
            ),
            'status'     => (bool) $worker['status'],
            'roles'      => explode(',', $worker['roles'])
        ));
    }

    /**
     * @Route("/api/admin/workers")
     * @Method("GET")
     *
     * @param Application $app
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function getWorkers(Application $app) {

        $workers = $app['db']->fetchAll('
          SELECT
            workers.id, 
            CONCAT(workers.last, \' \',  workers.first, \' \', workers.second) AS full_name, 
            professions.name AS profession,
            workers.roles,
            workers.status
          FROM
            `workers`
          JOIN
            `professions`
          ON
            professions.id = workers.profession_id
          WHERE 
            workers.id > 1
        ');

        if (empty($workers)) throw new AcmeNotFound('empty_workers');

        for ($i = 0; $i < count($workers); $i++) {

            settype($workers[$i]['id'], 'int');
            $workers[$i]['profession'] = array('name' => $workers[$i]['profession']);
            $workers[$i]['roles'] = explode(',', $workers[$i]['roles']);
            settype($workers[$i]['status'], 'bool');
        }

        return $app->json($workers);
    }

    /**
     * @Route("/api/nurse/doctors")
     * @Method("GET")
     *
     * @param Application $app
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function getDoctors(Application $app) {

        $workers = $app['db']->fetchAll('
          SELECT
            workers.id, 
            CONCAT(workers.last, \' \',  workers.first, \' \', workers.second) AS full_name, 
            professions.name AS profession,
            professions.id AS profession_id
          FROM
            `workers`
          JOIN
            `professions`
          ON
            professions.id = workers.profession_id
          WHERE 
            `roles` LIKE \'%ROLE_DOCTOR%\'
          AND 
            `status` = TRUE
        ');

        if (empty($workers)) throw new AcmeNotFound('empty_doctors');

        for ($i = 0; $i < count($workers); $i++) {

            settype($workers[$i]['id'], 'int');
            $workers[$i]['profession'] = array(
                'id'   => intval($workers[$i]['profession_id']),
                'name' => $workers[$i]['profession']
            );
            unset($workers[$i]['profession_id']);
        }

        return $app->json($workers);
    }

    /**
     * @Route("/api/doctor/protocol/list")
     * @Method("GET")
     *
     * @param Application $app
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function getProtocolList(Application $app) {

        $protocols = $app['db']->fetchAll('
          SELECT
            protocols.name,
            protocols.table
          FROM
            protocols
          JOIN 
            professions_protocols
          ON
            protocols.id = professions_protocols.protocol_id
          WHERE
            professions_protocols.profession_id IN (:id)
        ', array('id' => $app['user']->getProfession()->getId()));

        if (empty($protocols)) throw new AcmeNotFound('empty_protocols');

        return $app->json($protocols);
    }


    /**
     * @param Application $app
     * @param Worker $user
     * @param string $password
     * @throws AcmeException
     */
    private function sendUserLetter(Application $app, Worker $user, $password) {

        $respected = $user->isSex() ? $app->trans('mail_respected_man') : $app->trans('mail_respected_woman');

        $body = $app->render('/email/sing_in.html.twig', array(
            'name'     => $respected . ' ' . $user->getFirst() . ' ' . $user->getSecond(),
            'login'    => $user->getPhone(),
            'password' => $password
        ));

        $letter = Swift_Message::newInstance();

        $letter->setSubject($app->trans('mail_subject'));
        $letter->setFrom(array($app->trans('mail_feedback')));
        $letter->setTo(array($user->getEmail()));
        $letter->setBody($body->getContent(), 'text/html');

        if (!$app->mail($letter)) throw new AcmeException($app->trans('not_send_email'));
    }
}
