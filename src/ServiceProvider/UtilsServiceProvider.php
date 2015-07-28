<?php
/**
 * @author pdziok
 */
namespace ServiceProvider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Utils\DataMapper;

class UtilsServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     */
    public function register(Application $app)
    {
        $app['article.params.mapper'] = $app->share(function () {
            return new DataMapper([
                'id' => 'id',
                'remote_id' => 'remote_id',
                'title' => 'title',
                'created' => 'created',
                'updated' => 'updated',
                'published' => 'published',
                'url' => 'url',
                'status' => 'status',
                'media_house' => 'media_house',
                'publication' => 'publication',
            ]);
        });

        $app['tag.params.mapper'] = $app->share(function () {
            return new DataMapper([
                'id' => 'id',
                'remote_id' => 'remote_id',
                'name' => 'name',
                'description' => 'description',
                'slug' => 'slug',
                'created' => 'created',
                'tagType' => 'tagType',
                'media_house' => 'media_house',
                'publication' => 'publication',
            ]);
        });

        $app['user.params.mapper'] = $app->share(function () {
            return new DataMapper([
                'id' => 'id',
                'name' => 'name',
                'email' => 'email',
                'media_house' => 'media_house',
                'publication' => 'publication',
            ]);
        });
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {

    }
}
