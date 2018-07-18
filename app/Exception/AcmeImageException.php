<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 07.03.17
 * Time: 21:17
 */

namespace Acme\Exception;


final class AcmeImageException extends AcmeException {

    public function __construct($code = 0, $message = "") {
        parent::__construct($message, $code);
    }
}
