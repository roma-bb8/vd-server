<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 15.01.17
 * Time: 13:25
 */

namespace Acme\Api;


use Acme\Exception\AcmeInvalidParameterException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationList;

abstract class Controller {

    /**
     * @param Request $request
     * @return array
     * @throws AcmeInvalidParameterException
     */
    public function getDataFromRequest(Request $request) {

        $data = json_decode($request->getContent(), true);

        if ($data === null) throw new AcmeInvalidParameterException('json', 'json_incorrect');

        return $data;
    }

    /**
     * @param Application $app
     * @param object $object
     * @throws AcmeInvalidParameterException
     */
    public function checkObject(Application $app, $object) {

        /** @var ConstraintViolationList $errors */
        $errors = $app['validator']->validate($object);

        if (count($errors) == 0) return;

        throw new AcmeInvalidParameterException($errors[0]->getPropertyPath(), $errors[0]->getMessage());
    }
}
