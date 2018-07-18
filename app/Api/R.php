<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 20.01.17
 * Time: 23:21
 */

namespace Acme\Api;


use RedBeanPHP\Facade as db;

final class R {

    /**
     * @param Application $app
     */
    public static function init(Application $app) {

        $config = $app['config']['dbs.options'];

        db::setup('mysql:host=' . $config['local']['host'] . ';dbname=' . $config['local']['dbname'], $config['local']['user'], $config['local']['password']);

        db::freeze(array(
            'professions',
            'workers',
            'patients',
            'protocols',
            'services',
            'receptions',
            'professions_services',
            'professions_protocols',
            'receptions_protocols',
            'receptions_services'
        ));
    }
}
