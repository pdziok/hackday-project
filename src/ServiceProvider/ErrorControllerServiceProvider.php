<?php
/**
 * @author pdziok
 */

namespace ServiceProvider;

use HackdayProject\ErrorController;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ErrorControllerServiceProvider implements ServiceProviderInterface
{

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     *
     * @param Application $app
     */
    public function boot(Application $app)
    {

    }

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['error.controller'] = $controller = new ErrorController($app);

        $app->error([$controller, 'onApiProblemException']);
        $app->error([$controller, 'whenNotFound']);
        $app->error([$controller, 'whenNotAllowed']);
        $app->error([$controller, 'onHttpException']);
        $app->error([$controller, 'onGeneralException']);
    }
}
