<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 15.01.17
 * Time: 1:45
 */

namespace Acme\Api;


use Silex\Application as BaseApplication;

final class Application extends BaseApplication {

    use BaseApplication\MonologTrait;
    use BaseApplication\SecurityTrait;
    use BaseApplication\TranslationTrait;
    use BaseApplication\SwiftmailerTrait;
    use BaseApplication\TwigTrait;
}
