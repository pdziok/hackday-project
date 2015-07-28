<?php
/**
 * @author pdziok
 */
namespace HackdayProject;

use Silex\Application as SilexApplication;
use Silex\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ApiProblem\ApiProblemException;
use Crell\ApiProblem\ApiProblem;

class ErrorController
{
    /** @var  SilexApplication */
    private $app;

    /**
     * @param SilexApplication $app
     */
    public function __construct(SilexApplication $app)
    {
        $this->app = $app;
    }

    /**
     * Creates and return new ApiProblem instance
     *
     * @param  string     $title  title
     * @param  int        $status status code
     * @param  string     $detail description
     * @return ApiProblem
     */
    private function apiProblem($title, $status, $detail = null)
    {
        $problem = new ApiProblem($title, 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html');
        if ($detail) {
            $problem->setDetail($detail);
        }
        $problem->setStatus($status);

        return $problem;
    }

    /**
     * Creates new api problem JsonResponse
     *
     * @param  string       $title  title
     * @param  int          $status status code
     * @param  string       $detail description
     * @return JsonResponse
     */
    public function createApiProblemResponse($title, $status, $detail = null)
    {
        return new JsonResponse(
            $this->apiProblem($title, $status)->asArray(),
            $status,
            ['Content-Type' => 'application/problem+json']
        );
    }

    public function whenNotFound(NotFoundHttpException $e)
    {
        $response = $this->createApiProblemResponse('Endpoint not found', Response::HTTP_NOT_FOUND);
        $response->headers->add($e->getHeaders());

        return $response;
    }

    public function whenNotAllowed(MethodNotAllowedHttpException $e)
    {
        $response = $this->createApiProblemResponse($e->getMessage(), Response::HTTP_METHOD_NOT_ALLOWED);
        $response->headers->add($e->getHeaders());

        return $response;
    }

    public function onHttpException(HttpException $e)
    {
        $errorMessage = [
            'message' => $e->getMessage(),
            'error' => [
                    'errorCode' => $e->getStatusCode(),
                ] + $this->dumpExceptionsRecursively($e)
        ];

        return new JsonResponse($errorMessage, $e->getStatusCode(), $e->getHeaders());
    }

    public function onGeneralException(\Exception $e)
    {
        return $this->onHttpException(new HttpException(500, 'Internal API Error', $e));
    }

    public function onApiProblemException(ApiProblemException $e)
    {
        return $this->createApiProblemResponse($e->getTitle(), $e->getStatus(), $e->getDetail());
    }

    private function dumpExceptionsRecursively(\Exception $e)
    {
        if (!$this->app['debug']) {
            return [];
        }

        return [
            'title' => $e->getMessage(),
            'previous' => $e->getPrevious() ? $this->dumpExceptionsRecursively($e->getPrevious()) : null,
            'stack' => $e->getTrace(),
        ];
    }

    /**
     * @apiDefine InternalServerError
     * @apiError (Error 5xx) InternalServerError An error occured during saving node.
     * @apiErrorExample InternalServerError:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *       "error": "Internal Server Error"
     *     }
     */
}
