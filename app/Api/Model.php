<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 19.01.17
 * Time: 13:54
 */

namespace Acme\Api;


abstract class Model {

    const EMPTY_MESSAGE = 'field_absent';

    /**
     * @param string $str
     * @return string
     */
    public function formatName($str) {
        return mb_convert_case($str, MB_CASE_TITLE, 'UTF-8');
    }
}
