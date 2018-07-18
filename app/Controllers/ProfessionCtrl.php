<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 15.01.17
 * Time: 13:34
 */

namespace Acme\Controllers;


use Acme\Api\Application;
use Acme\Api\Controller;
use Acme\Exception\AcmeDBALException;
use Acme\Exception\AcmeException;
use Acme\Exception\AcmeInvalidParameterException;
use Acme\Exception\AcmeNotFound;
use Acme\Models\Profession;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\DriverException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfessionCtrl extends Controller {

    /**
     * @Route("/api/admin/profession")
     * @Method("POST")
     *
     * @param Application $app
     * @param Request $request
     * @return JsonResponse
     * @throws AcmeInvalidParameterException
     * @throws AcmeDBALException
     */
    public function create(Application $app, Request $request) {

        $data = $this->getDataFromRequest($request);

        $profession = new Profession();

        $profession->setName($data['name']);

        $this->checkObject($app, $profession);

        try {

            $app['db']->insert('professions', array(
                'name'             => $profession->getName(),
                'info_creating_id' => $app['user']->getId(),
                'info_changed_id'  => $app['user']->getId()
            ));

        } catch (DriverException $error) {

            if ($error->getErrorCode() === 1062) {
                throw new AcmeInvalidParameterException('name', 'duplicate_profession');
            } else {
                throw new AcmeDBALException($app, $error->getErrorCode(), $error->getMessage());
            }
        }

        return $app->json(array(
            'id'   => (int) $app['db']->lastInsertId(),
            'name' => $profession->getName()
        ), Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/admin/profession")
     * @Method("PUT")
     *
     * @param Application $app
     * @param Request $request
     * @return JsonResponse
     * @throws AcmeNotFound
     * @throws AcmeInvalidParameterException
     * @throws AcmeDBALException
     */
    public function update(Application $app, Request $request) {

        $data = $this->getDataFromRequest($request);

        $profession = new Profession();

        $profession->setId($data['id']);
        $profession->setName($data['name']);

        $this->checkObject($app, $profession);

        try {

            $update = $app['db']->update('professions', array(
                'name'            => $profession->getName(),
                'info_changed_id' => $app['user']->getId()
            ), array('id' => $profession->getId()));

            if ($update === 0) throw new AcmeNotFound('profession_not_update', $profession->getId());

        } catch (DriverException $error) {

            if ($error->getErrorCode() === 1062) {
                throw new AcmeInvalidParameterException('name', 'duplicate_profession');
            } else {
                throw new AcmeDBALException($app, $error->getErrorCode(), $error->getMessage());
            }
        }

        return $app->json(array(
            'id'   => $profession->getId(),
            'name' => $profession->getName()
        ));
    }

    /**
     * @Route("/api/admin/profession/{id}")
     * @Method("DELETE")
     *
     * @param Application $app
     * @param integer $id
     * @return JsonResponse
     * @throws AcmeException
     * @throws AcmeNotFound
     */
    public function delete(Application $app, $id) {

        if ($id < 2) throw new AcmeException($app->trans('profession_not_delete_root'), Response::HTTP_FORBIDDEN);

        if ($app['db']->delete('professions', array('id' => $id))) return $app->json(null, Response::HTTP_NO_CONTENT);

        throw new AcmeNotFound('empty_profession_id', $id);
    }

    /**
     * @Route("/api/admin/profession/{id}")
     * @Method("GET")
     *
     * @param Application $app
     * @param integer $id
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function getByID(Application $app, $id) {

        $profession = $app['db']->fetchAssoc('
          SELECT
            *
          FROM
            professions
          WHERE
            id IN (:id)
        ', array('id' => $id));

        if (empty($profession)) throw new AcmeNotFound('empty_profession_id', $id);

        return $app->json(array(
            'id'   => (int) $profession['id'],
            'name' => $profession['name']
        ));
    }

    /**
     * @Route("/api/admin/professions")
     * @Method("GET")
     *
     * @param Application $app
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function getProfessions(Application $app) {

        $professions = $app['db']->fetchAll('
          SELECT
            id, name
          FROM
            professions
          WHERE
            id > 1
        ');

        if (empty($professions)) throw new AcmeNotFound('empty_professions');

        for ($i = 0; $i < count($professions); $i++) settype($professions[$i]['id'], 'int');

        return $app->json($professions);
    }

    /**
     * @Route("/api/admin/profession/binding")
     * @Method("PUT")
     *
     * @param Application $app
     * @param Request $request
     * @return JsonResponse
     * @throws AcmeDBALException
     * @throws AcmeInvalidParameterException
     * @throws AcmeNotFound
     */
    public function setDependencies(Application $app, Request $request) {

        $data = $this->getDataFromRequest($request);

        /** @var Connection $conn */
        $conn = $app['db'];

        $profession = $conn->fetchAssoc('SELECT id FROM professions WHERE id IN (?) ', array($data['profession_id']));
        if (empty($profession)) throw new AcmeNotFound('empty_profession_id', $data['profession_id']);

        $sql = 'SELECT id FROM services WHERE id IN (?)';
        $query = $conn->executeQuery($sql, array($data['service_ids']), array(Connection::PARAM_INT_ARRAY));
        $services = $query->fetchAll();
        if (count($services) !== count($data['service_ids'])) throw new AcmeNotFound('empty_services');

        $sql = 'SELECT id FROM protocols WHERE id IN (?)';
        $query = $conn->executeQuery($sql, array($data['protocol_ids']), array(Connection::PARAM_INT_ARRAY));
        $protocols = $query->fetchAll();
        if (count($protocols) !== count($data['protocol_ids'])) throw new AcmeNotFound('empty_protocols');

        $conn->delete('professions_protocols', array('profession_id' => $profession['id']));
        $conn->delete('professions_services', array('profession_id' => $profession['id']));

        foreach ($protocols as $protocol) {
            $conn->insert('professions_protocols', array(
                'profession_id'    => $profession['id'],
                'protocol_id'      => $protocol['id'],
                'info_creating_id' => $app['user']->getId(),
                'info_changed_id'  => $app['user']->getId()
            ));
        }

        foreach ($services as $service) {
            $conn->insert('professions_services', array(
                'profession_id'    => $profession['id'],
                'service_id'       => $service['id'],
                'info_creating_id' => $app['user']->getId(),
                'info_changed_id'  => $app['user']->getId()
            ));
        }

        return $app->json(array(
            'message' => $app->trans('professions_dependencies')
        ), Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/admin/profession/binding")
     * @Method("GET")
     *
     * @param Application $app
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function getDependencies(Application $app) {

        $professions = $app['db']->fetchAll('
          SELECT
            professions.id AS id,
            professions.name AS name,
            GROUP_CONCAT(
              DISTINCT 
                professions_services.service_id 
              ORDER BY 
                professions_services.service_id ASC 
              SEPARATOR 
                \',\'
            ) AS service_ids,
            GROUP_CONCAT(
              DISTINCT 
                professions_protocols.protocol_id
              ORDER BY
                professions_protocols.protocol_id ASC 
              SEPARATOR 
                \',\'
            ) AS protocol_ids
          FROM
            professions
          LEFT JOIN
            professions_services
          ON
            professions.id = professions_services.profession_id
          LEFT JOIN
            professions_protocols
          ON
            professions.id = professions_protocols.profession_id
          WHERE
            professions.id > 1
          GROUP BY
            professions.id
        ');

        if (empty($professions)) throw new AcmeNotFound('empty_professions');

        for ($i = 0; $i < count($professions); $i++) {

            settype($professions[$i]['id'], 'int');

            $professions[$i]['service_ids'] = array_map('intval', array_filter(explode(',', $professions[$i]['service_ids'])));
            $professions[$i]['protocol_ids'] = array_map('intval', array_filter(explode(',', $professions[$i]['protocol_ids'])));
        }

        return $app->json($professions);
    }
}
