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
use Acme\Exception\AcmeDBALException;
use Acme\Exception\AcmeInvalidParameterException;
use Acme\Exception\AcmeNotFound;
use Acme\Models\Service;
use Doctrine\DBAL\Exception\DriverException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceCtrl extends Controller {

    /**
     * @Route("/api/admin/service")
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

        $service = new Service();

        $service->setName($data['name']);
        $service->setPrice($data['price']);

        $this->checkObject($app, $service);

        try {

            $app['db']->insert('services', array(
                'name'             => $service->getName(),
                'price'            => $service->getPrice(),
                'info_creating_id' => $app['user']->getId(),
                'info_changed_id'  => $app['user']->getId()
            ));

        } catch (DriverException $error) {

            if ($error->getErrorCode() === 1062) {
                throw new AcmeInvalidParameterException('name', 'duplicate_service');
            } else {
                throw new AcmeDBALException($app, $error->getErrorCode(), $error->getMessage());
            }
        }

        return $app->json(array(
            'id'    => (int) $app['db']->lastInsertId(),
            'name'  => $service->getName(),
            'price' => $service->getPrice(),
        ), Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/admin/service")
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

        $service = new Service();

        $service->setId($data['id']);
        $service->setName($data['name']);
        $service->setPrice($data['price']);

        $this->checkObject($app, $service);

        try {

            $update = $app['db']->update('services', array(
                'name'            => $service->getName(),
                'price'           => $service->getPrice(),
                'info_changed_id' => $app['user']->getId()
            ), array('id' => $service->getId()));

            if ($update === 0) throw new AcmeNotFound('empty_service_id', $service->getId());

        } catch (DriverException $error) {

            if ($error->getErrorCode() === 1062) {
                throw new AcmeInvalidParameterException('name', 'duplicate_service');
            } else {
                throw new AcmeDBALException($app, $error->getErrorCode(), $error->getMessage());
            }
        }

        return $app->json(array(
            'id'    => $service->getId(),
            'name'  => $service->getName(),
            'price' => $service->getPrice(),
        ));
    }

    /**
     * @Route("/api/admin/service/{id}")
     * @Method("DELETE")
     *
     * @param Application $app
     * @param integer $id
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function delete(Application $app, $id) {

        if ($app['db']->delete('services', array('id' => $id))) return $app->json(null, Response::HTTP_NO_CONTENT);

        throw new AcmeNotFound('empty_service_id', $id);
    }

    /**
     * @Route("/api/admin/service/{id}")
     * @Method("GET")
     *
     * @param Application $app
     * @param integer $id
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function getByID(Application $app, $id) {

        $service = $app['db']->fetchAssoc('
          SELECT
            *
          FROM
            services
          WHERE
            id IN (:id)
        ', array('id' => $id));

        if (empty($service)) throw new AcmeNotFound('empty_service_id', $id);

        return $app->json(array(
            'id'    => (int) $service['id'],
            'name'  => $service['name'],
            'price' => (float) $service['price']
        ));
    }

    /**
     * @Route("/api/admin/services")
     * @Method("GET")
     *
     * @param Application $app
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function getServices(Application $app) {

        $services = $app['db']->fetchAll('
          SELECT
            id, name, price
          FROM
            services
        ');

        if (empty($services)) throw new AcmeNotFound('empty_services');

        for ($i = 0; $i < count($services); $i++) {
            settype($services[$i]['id'], 'int');
            settype($services[$i]['price'], 'float');
        }

        return $app->json($services);
    }
}
