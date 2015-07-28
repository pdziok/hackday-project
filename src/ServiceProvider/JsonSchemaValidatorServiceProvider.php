<?php
/**
 * @author pdziok
 */

namespace ServiceProvider;

use JsonSchema\RefResolver;
use JsonSchema\Uri\UriRetriever;
use JsonSchema\Validator;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class JsonSchemaValidatorServiceProvider implements ServiceProviderInterface
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
        $app['json.request.validator'] = $app->protect(function (Request $request) use ($app) {
            $action = $request->attributes->get('_controller');

            if (!is_string($action) || !isset($app['json.request.validator.schemaMap'][$action])) {
                //no schema map found for this url, skip validation
                return;
            }

            $schemaFile = $app['json.request.validator.schemaMap'][$action];
            $schemaPath = ROOT_PATH . '/web/schema/' . $schemaFile;
            $retriever = new UriRetriever();
            $schema = $retriever->retrieve('file://' . $schemaPath);

            // If you use $ref or if you are unsure, resolve those references here
            // This modifies the $schema object
            $refResolver = new RefResolver($retriever);
            $refResolver->resolve($schema, 'file://' . ROOT_PATH . '/web/schema/');


            $validator = new Validator();
            $validator->check(json_decode($request->getContent()), $schema);

            if ($validator->getErrors()) {
                $schemaPublicUrl = $request->getSchemeAndHttpHost() . '/' .
                    $request->getBaseUrl() . '/schema/' . $schemaFile;

                return new JsonResponse(
                    [
                        'error' => 'Data contains errors',
                        'details' => $validator->getErrors(),
                        'schemaUrl' => $schemaPublicUrl
                    ],
                    400
                );
            }
        });
    }
}
