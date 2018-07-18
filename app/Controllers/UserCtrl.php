<?php
/**
 * Created by PhpStorm.
 * UserModel: sommelier
 * Date: 15.01.17
 * Time: 13:29
 */

namespace Acme\Controllers;


use Acme\Api\Application;
use Acme\Api\Controller;
use Acme\Api\Model;
use Acme\Exception\AcmeAuthorisationException;
use Acme\Exception\AcmeDBALException;
use Acme\Exception\AcmeInvalidParameterException;
use Acme\Exception\AcmeUnsupportedException;
use Acme\Models\Worker;
use Acme\Provider\PasswordProvider;
use Doctrine\DBAL\Exception\DriverException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserCtrl extends Controller {

    /**
     * @Route("/api/login")
     * @Method("POST")
     *
     * @param Application $app
     * @param Request $request
     * @return JsonResponse
     * @throws AcmeInvalidParameterException
     * @throws AcmeAuthorisationException
     */
    public function getUserAndToken(Application $app, Request $request) {

        $data = $this->getDataFromRequest($request);

        if (empty($data['phone'])) throw new AcmeInvalidParameterException('phone', Model::EMPTY_MESSAGE);
        if (empty($data['password'])) throw new AcmeInvalidParameterException('password', Model::EMPTY_MESSAGE);

        /** @var Worker $user */
        $user = $app['users']->loadUserByUsername(str_replace('+', '', $data['phone']));

        $is = $app['security.encoder.digest']->isPasswordValid($user->getPassword(), $data['password'], $user->getSalt());

        if (!$is) throw new AcmeAuthorisationException($app->trans('user_absent', array('%user%' => $data['phone'])));

        return $app->json(array(
            'id'         => $user->getId(),
            'first'      => $user->getFirst(),
            'second'     => $user->getSecond(),
            'last'       => $user->getLast(),
            'sex'        => $user->isSex(),
            'birthday'   => $user->getBirthday(),
            'phone'      => $user->getPhone(),
            'email'      => $user->getEmail(),
            'profession' => array(
                'id'   => $user->getProfession()->getId(),
                'name' => $user->getProfession()->getName()
            ),
            'status'     => $user->isStatus(),
            'roles'      => $user->getRoles(),
            'token'      => $app['security.jwt.encoder']->encode(array('phone' => $user->getPhone()))
        ));
    }

    /**
     * @Route("/api/update")
     * @Method("PUT")
     *
     * @param Application $app
     * @param Request $request
     * @return JsonResponse
     * @throws AcmeInvalidParameterException
     * @throws AcmeAuthorisationException
     * @throws AcmeDBALException
     */
    public function updateYourAccount(Application $app, Request $request) {

        $data = $this->getDataFromRequest($request);

        if (empty($data['password'])) throw new AcmeInvalidParameterException('password', Model::EMPTY_MESSAGE);

        /** @var Worker $user */
        $user = $app['user'];

        $is = $app['security.encoder.digest']->isPasswordValid($user->getPassword(), $data['password'], $user->getSalt());

        if (!$is) throw new AcmeAuthorisationException($app->trans('invalid_password'));

        if (!empty($data['password_new'])) {

            $passwordProvider = new PasswordProvider(array('password' => $data['password_new']));

            $user->setPassword($passwordProvider->getHashPassword($user));
        }

        $user->setFirst($data['first']);
        $user->setSecond($data['second']);
        $user->setLast($data['last']);
        $user->setSex($data['sex']);
        $user->setBirthday($data['birthday']);
        $user->setEmail($data['email']);
        if ($user->getPhone() !== $data['phone']) $user->setPhone($data['phone']);

        $this->checkObject($app, $user);

        try {

            $new = array(
                'first'           => $user->getFirst(),
                'second'          => $user->getSecond(),
                'last'            => $user->getLast(),
                'sex'             => $user->isSex(),
                'birthday'        => $user->getBirthday(),
                'email'           => $user->getEmail(),
                'password'        => $user->getPassword(),
                'info_changed_id' => $user->getId()
            );

            if ($user->getPhone() !== $app['user']->getPhone()) {
                $new = array('phone' => $user->getPhone());
            }

            $app['db']->update('workers', $new, array('id' => $user->getId()));

        } catch (DriverException $error) {

            if ($error->getErrorCode() === 1062) {
                throw new AcmeInvalidParameterException('phone', 'duplicate_phone');
            } else {
                throw new AcmeDBALException($app, $error->getErrorCode(), $error->getMessage());
            }
        }

        return $app->json(array(
            'id'       => $user->getId(),
            'first'    => $user->getFirst(),
            'second'   => $user->getSecond(),
            'last'     => $user->getLast(),
            'sex'      => $user->isSex(),
            'birthday' => $user->getBirthday(),
            'phone'    => $user->getPhone(),
            'email'    => $user->getEmail()
        ));
    }
}
