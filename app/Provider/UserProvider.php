<?php
/**
 * Created by PhpStorm.
 * User: maestro
 * Date: 21.10.16
 * Time: 1:36
 */

namespace Acme\Provider;


use Acme\Api\Application;
use Acme\Exception\AcmeAuthorisationException;
use Acme\Exception\AcmeUnsupportedException;
use Acme\Models\Worker;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface {

    /**
     * @var Application
     */
    private $app;


    /**
     * UserProvider constructor.
     * @param Application $app
     */
    public function __construct(Application $app) {
        $this->app = $app;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The phone for user
     * @return UserInterface
     * @throws AcmeAuthorisationException
     */
    public function loadUserByUsername($username) {

        $user = $this->app['db']->fetchAssoc('
          SELECT
            workers.id,
            workers.first,
            workers.second,
            workers.last,
            workers.sex,
            workers.birthday,
            workers.phone,
            workers.salt,
            workers.password,
            workers.email,
            professions.id AS profession_id,
            professions.name AS profession_name,
            workers.status,
            workers.roles
          FROM
            `workers`
          JOIN
            `professions`
          ON
            professions.id = workers.profession_id
          WHERE
            `phone` = :phone
          AND
            `status` = TRUE
        ', array('phone' => $username));

        if (empty($user)) throw new AcmeAuthorisationException($this->app->trans('user_absent', array('%user%' => $username)));

        $worker = new Worker();

        $worker->setId($user['id']);
        $worker->setFirst($user['first']);
        $worker->setSecond($user['second']);
        $worker->setLast($user['last']);
        $worker->setSex((bool) $user['sex']);
        $worker->setBirthday($user['birthday']);
        $worker->setPhone($user['phone']);
        $worker->setSalt($user['salt']);
        $worker->setPassword($user['password']);
        $worker->setEmail($user['email']);
        $worker->getProfession()->setId($user['profession_id']);
        $worker->getProfession()->setName($user['profession_name']);
        $worker->setStatus((bool) $user['status']);
        $worker->setRoles(explode(',', $user['roles']));

        return $worker;
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     * @return UserInterface
     * @throws AcmeAuthorisationException
     */
    public function refreshUser(UserInterface $user) {

        if (!$user instanceof Worker) throw new AcmeAuthorisationException($this->app->trans('not_user_obj'));

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     * @return bool
     */
    public function supportsClass($class) {
        return $class === Worker::class;
    }
}
