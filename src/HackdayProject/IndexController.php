<?php
/**
 * @author pdziok
 */
namespace HackdayProject;

use Silex\Application as SilexApplication;
use Silex\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class IndexController
{
    /**
     * List possible endpoints.
     *
     * @param SilexApplication $app
     * @return JsonResponse
     */
    public function getIndexAction(SilexApplication $app)
    {
        $routes = [];

        $patterns = $app['routes']->getIterator();
        /** @var Route $pattern */
        foreach ($patterns as $pattern) {
            $method = implode(',', $pattern->getMethods());
            if (preg_match('/OPTIONS/', $method)) {
                continue;
            }

            $route = [
                'path' => $pattern->getPath(),
                'method' => $method
            ];

            $controller = $pattern->getDefault('_controller');

            if (is_string($controller)) {
                list($controller, $method) = explode(':', $controller);
                $controllerService = $app[$controller];
                $reflection = new \ReflectionClass($controllerService);
                $reflectionMethod = $reflection->getMethod($method);
                $route['description'] = $this->parseDescription($reflectionMethod);
            }

            $routes[] = $route;
        }

        return new JsonResponse($routes);
    }

    /**
     * @param $reflectionMethod
     * @return string
     */
    private function parseDescription(\ReflectionMethod $reflectionMethod)
    {
        $docComment = $reflectionMethod->getDocComment();
        $docComment = explode("\n", $docComment);
        $docComment = array_slice($docComment, 1, -1);
        $docComment = array_map(function ($doc) {
            $doc = ltrim($doc, ' *');
            if (preg_match('/^@/', $doc)) {
                return false;
            }
            return $doc;
        }, $docComment);
        $docComment = array_filter($docComment);

        return implode(' ', $docComment);
    }
}
