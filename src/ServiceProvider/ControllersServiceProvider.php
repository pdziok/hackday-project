<?php
/**
 * @author pdziok
 */
namespace ServiceProvider;

use HackdayProject\IndexController;
use Silex\Application as SilexApplication;
use Silex\ServiceProviderInterface;

class ControllersServiceProvider implements ServiceProviderInterface
{
    private $controllers = [];

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     */
    public function register(SilexApplication $app)
    {
        $this->registerIndexController($app);
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(SilexApplication $app)
    {
    }

    /**
     * @param SilexApplication $app
     * @return array
     */
    private function registerIndexController(SilexApplication $app)
    {
        $serviceName = 'index.controller';
        $app[$serviceName] = $app->share(function () {
            return new IndexController();
        });
        $this->controllers[$serviceName] = IndexController::class;
    }
}
