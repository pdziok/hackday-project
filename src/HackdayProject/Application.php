<?php
namespace HackdayProject;

use Dbtlr\MigrationProvider\Provider\MigrationServiceProvider;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use JDesrosiers\Silex\Provider\CorsServiceProvider;
use Igorw\Silex\ConfigServiceProvider;
use Knp\Provider\ConsoleServiceProvider;
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
            'db.options' => $this['config']['mysql']
        ]);

        $this->register(new ConsoleServiceProvider(), $this['config']['console']);
        $this->register(new MigrationServiceProvider(), [
            'db.migrations.path' => ROOT_PATH . '/resources/migrations',
        ]);
        $this->register(new DoctrineOrmServiceProvider, $this['config']['doctrine.orm']);
    }
}
