<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 15.01.17
 * Time: 13:50
 */

namespace Acme\Controllers;


use Acme\Api\Application;
use Acme\Api\Controller;
use Acme\Exception\AcmeDBALException;
use Acme\Exception\AcmeInvalidParameterException;
use Acme\Exception\AcmeNotFound;
use Acme\Models\Protocol;
use Doctrine\DBAL\Exception\DriverException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProtocolCtrl extends Controller {

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

        $protocol = new Protocol();

        $protocol->setId($data['id']);
        $protocol->setName($data['name']);
        $protocol->setType($data['type']);

        $this->checkObject($app, $protocol);

        try {

            $update = $app['db']->update('protocols', array(
                'name'            => $protocol->getName(),
                'type'            => $protocol->getType(),
                'info_changed_id' => $app['user']->getId()
            ), array('id' => $protocol->getId()));

            if ($update === 0) throw new AcmeNotFound('protocol_not_update', $protocol->getId());

        } catch (DriverException $error) {

            if ($error->getErrorCode() === 1062) {
                throw new AcmeInvalidParameterException('name', 'duplicate_protocol');
            } else {
                throw new AcmeDBALException($app, $error->getErrorCode(), $error->getMessage());
            }
        }

        return $app->json(array(
            'id'   => $protocol->getId(),
            'name' => $protocol->getName(),
            'type' => $protocol->getType()
        ));
    }

    /**
     * @Route("/api/admin/profession")
     * @Method("GET")
     *
     * @param Application $app
     * @param integer $id
     * @return JsonResponse
     * @throws AcmeNotFound
     */
    public function getByID(Application $app, $id) {

        $protocol = $app['db']->fetchAssoc('
          SELECT
            *
          FROM
            protocols
          WHERE
            id IN (:id)
        ', array('id' => $id));

        if (empty($protocol)) throw new AcmeNotFound('empty_protocol_id', $id);

        return $app->json(array(
            'id'    => (int) $protocol['id'],
            'table' => $protocol['table'],
            'name'  => $protocol['name'],
            'type'  => $protocol['type']
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
    public function getProtocols(Application $app) {

        $protocols = $app['db']->fetchAll('
          SELECT
            `id`, `name`, `table`, `type`
          FROM
            protocols
        ');

        if (empty($protocols)) throw new AcmeNotFound('empty_protocols');

        for ($i = 0; $i < count($protocols); $i++) settype($protocols[$i]['id'], 'int');

        return $app->json($protocols);
    }
}
