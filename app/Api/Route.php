<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 15.01.17
 * Time: 17:05
 */

namespace Acme\Api;


use Silex\Route as BaseRoute;

final class Route extends BaseRoute {

    use BaseRoute\SecurityTrait;
}
