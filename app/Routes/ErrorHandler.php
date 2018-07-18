<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 26.02.17
 * Time: 11:54
 */

namespace Acme\Routes;


use Acme\Api\Application as App;
use Acme\Exception\AcmeAuthorisationException;
use Acme\Exception\AcmeDBALException;
use Acme\Exception\AcmeImageException;
use Acme\Exception\AcmeInvalidParameterException;
use Acme\Exception\AcmeNotFound;
use Exception;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ErrorHandler implements ControllerProviderInterface {

    /**
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app) {

        /** @var App $app */
        $app->error(function (Exception $error, Request $request, $code) use ($app) {

            if ($error instanceof AcmeInvalidParameterException) {

                return $app->json(array(
                    'error' => $app->trans($error->getDescription())
                ), Response::HTTP_BAD_REQUEST);
            }

            elseif ($error instanceof AcmeNotFound) {

                return $app->json(array(
                    'error' => $app->trans($error->getMessageID(), array('%id%' => $error->getID()))
                ), Response::HTTP_NOT_FOUND);
            }

            elseif ($error instanceof AcmeAuthorisationException) {

                return $app->json(array(
                    'error' => $error->getMessage()
                ), Response::HTTP_UNAUTHORIZED);
            }

            elseif ($error instanceof AcmeImageException) {

                if ($error->getCode() === UPLOAD_ERR_INI_SIZE) $message = 'acme_image_exception_upload_err_ini_size';
                elseif ($error->getCode() === UPLOAD_ERR_FORM_SIZE) $message = 'acme_image_exception_upload_err_form_size';
                elseif ($error->getCode() === UPLOAD_ERR_PARTIAL) $message = 'acme_image_exception_upload_err_partial';
                elseif ($error->getCode() === UPLOAD_ERR_NO_TMP_DIR) $message = 'acme_image_exception_upload_err_no_tmp_dir';
                elseif ($error->getCode() === UPLOAD_ERR_CANT_WRITE) $message = 'acme_image_exception_upload_err_cant_write';
                else  $message = 'acme_image_exception_upload_not_images';

                return $app->json(array(
                    'error' => $app->trans($message, array('%name%' => $error->getMessage()))
                ), Response::HTTP_BAD_REQUEST);
            }

            elseif ($error instanceof AcmeDBALException) {

                return $app->json(array(
                    'error' => $error->getMessage()
                ), Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $app->json(array('error' => $error->getMessage()), $error->getCode() === 0 ? 500 : $error->getCode());
        });

        return $app['controllers_factory'];
    }
}
