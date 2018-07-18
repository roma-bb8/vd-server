<?php
/**
 * Created by PhpStorm.
 * User: Roma Baranenko (sommelier.jungle@gmail.com)
 * Date: 28.09.16
 * Time: 17:19
 */

if (preg_match('/\.(?:png|jpg|jpeg)$/', $_SERVER["REQUEST_URI"])) return false;

date_default_timezone_set('UTC');

require_once __DIR__ . '/../vendor/autoload.php';

use Acme\Api\Application;
use Acme\Api\Route;
use Acme\Exception\AcmeAuthorisationException;
use Acme\Routes\AdminRoutes;
use Acme\Routes\DoctorRoutes;
use Acme\Routes\NurseRoutes;
use Acme\Routes\ErrorHandler as ErrorHandlerRoute;
use Acme\Routes\UserRoutes;
use Acme\Models\Worker;
use Acme\Provider\UserProvider;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Lokhman\Silex\Provider\ConfigServiceProvider;
use Monolog\Logger;
use Silex\Component\Security\Http\Token\JWTToken;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityJWTServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Cache\Simple\ApcuCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Loader\JsonFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;

$app = new Application();

$app->register(new ConfigServiceProvider(), array(
    'config.dir' => __DIR__ . '/../app/Config'
));

$app['debug'] = $app['config']['debug'];
$app['route_class'] = Route::class;
$app['controllers_namespace'] = 'Acme\Controllers';
$app['users'] = function () use ($app) {
    return new UserProvider($app);
};
$app['security.jwt'] = array(
    'secret_key' => $app['config']['jwt']['secret.key'],
    'life_time'  => $app['config']['jwt']['life.time'],
    'algorithm'  => $app['config']['jwt']['algorithm'],
    'options'    => array(
        'username_claim' => $app['config']['jwt']['options']['username.claim'],
        'header_name'    => $app['config']['jwt']['options']['header.name'],
        'token_prefix'   => $app['config']['jwt']['options']['token.prefix'],
    )
);

ErrorHandler::register();
ExceptionHandler::register($app['debug']);

$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__ . '/../var/log/php/error.log',
    'monolog.name'    => 'Acme',
    'monolog.level'   => $app['config']['debug'] ? Logger::DEBUG : Logger::WARNING
));
$app->register(new SwiftmailerServiceProvider(), array(
    'swiftmailer.options' => array(
        'host'       => $app['config']['swift.mailer']['host'],
        'port'       => $app['config']['swift.mailer']['port'],
        'username'   => $app['config']['swift.mailer']['username'],
        'password'   => $app['config']['swift.mailer']['password'],
        'encryption' => $app['config']['swift.mailer']['encryption'],
        'auth_mode'  => $app['config']['swift.mailer']['auth.mode']
    )
));
$app->register(new SecurityServiceProvider(), array(
    'security.firewalls'      => array(
        'login'   => array(
            'pattern'   => 'login',
            'anonymous' => true,
        ),
        'secured' => array(
            'pattern' => '^.*$',
            'logout'  => array(
                'logout_path' => '/logout'
            ),
            'users'   => $app['users'],
            'jwt'     => array(
                'use_forward'              => true,
                'require_previous_session' => false,
                'stateless'                => true,
            )
        )
    ),
    'security.role_hierarchy' => array(
        Worker::ROLE_ADMIN       => array(
            Worker::ROLE_NURSE,
            Worker::ROLE_DOCTOR
        ),
        Worker::ROLE_SUPER_ADMIN => array(
            Worker::ROLE_ADMIN
        )
    ),
    'security.access_rules'   => array(
        array('^/api/admin', Worker::ROLE_ADMIN),
        array('^/api/nurse', Worker::ROLE_NURSE),
        array('^/api/doctor', Worker::ROLE_DOCTOR)
    )
));
$app->register(new ValidatorServiceProvider());
$app->register(new TranslationServiceProvider(), array(
    'locale_fallbacks' => array('ru'),
    'locale'           => $app['config']['locale']
));
$app->register(new SecurityJWTServiceProvider());
$app->register(new DoctrineServiceProvider());
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../app/Views',
));

$app['validator.mapping.class_metadata_factory'] = function () use ($app) {

    foreach (spl_autoload_functions() as $function) AnnotationRegistry::registerLoader($function);

    $reader = new AnnotationReader;
    $loader = new AnnotationLoader($reader);
    $cache = extension_loaded('apcu') ? new ApcuCache : null;

    return new LazyLoadingMetadataFactory($loader, $cache);
};
$app->extend('translator', function ($translator) {

    /** @var Translator $translator */
    $translator->addLoader('json', new JsonFileLoader());

    $translator->addResource('json', __DIR__ . '/../app/Locals/ru.json', 'ru');

    return $translator;
});
$app['translator.domains'] = array(
    'validators' => array(
        'ru' => json_decode(file_get_contents(__DIR__ . '/../app/Locals/Domains/ru.json'), true)
    )
);

$app->before(function (Request $request) use ($app) {

    if ($request->getRequestUri() !== '/api/login') {

        $token = $app['security.token_storage']->getToken();

        if (!$token instanceof JWTToken) throw new AcmeAuthorisationException('token not allowed.');

        $app['user'] = $token->getUser();
    }
});

$app->mount('/', new ErrorHandlerRoute());
$app->mount('/api', new UserRoutes());
$app->mount('/api/nurse', new NurseRoutes());
$app->mount('/api/doctor', new DoctorRoutes());
$app->mount('/api/admin', new AdminRoutes());

$app->run();
