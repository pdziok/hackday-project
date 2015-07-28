<?php
namespace HackdayProject;

use JDesrosiers\Silex\Provider\CorsServiceProvider;
use Igorw\Silex\ConfigServiceProvider;
use MJanssen\Provider\RoutingServiceProvider;
use ServiceProvider\ControllersServiceProvider;
use ServiceProvider\UtilsServiceProvider;
use ServiceProvider\ErrorControllerServiceProvider;
use ServiceProvider\JsonSchemaValidatorServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;

class Application extends \Silex\Application
{
    public function __construct(array $values = array())
    {
        parent::__construct($values);

        $this->register(
            new ConfigServiceProvider(__DIR__ . '/../../config/routes.php')
        );

        $this->register(new RoutingServiceProvider('hackday-project'));

        $this->register(new ServiceControllerServiceProvider());
        $this->register(new ControllersServiceProvider());

        $this->register(new ErrorControllerServiceProvider());

        $this->register(new CorsServiceProvider(), array(
            'cors.allowOrigin' => '*',
        ));
        $this->after($this['cors']);
//        $this->register(new JsonSchemaValidatorServiceProvider(), [
//            'json.request.validator.schemaMap' => [
//            ]
//        ]);
//        $this->before($this['json.request.validator']);

        $this->register(new DoctrineServiceProvider(), [
            'dbs.options' => $this['config']['mysql']
        ]);
    }
}
