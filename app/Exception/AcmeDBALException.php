<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 05.03.17
 * Time: 5:07
 */

namespace Acme\Exception;


use Acme\Api\Application;
use Monolog\Logger;

final class AcmeDBALException extends AcmeException {

    public function __construct(Application $app, $code = "", $message = "") {

        $app->log('DBALException', array('code' => $code, 'message' => $message), Logger::ERROR);

        parent::__construct($app['debug'] ? $message : $app->trans('something_went_wrong'));
    }
}
