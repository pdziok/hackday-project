<?php
/**
 * @author pdziok
 */
namespace ApiProblem;

use Symfony\Component\HttpFoundation\Response;

class InternalServerError extends ApiProblemException
{
    public function __construct(
        $message,
        $detail = null,
        \Exception $previous = null,
        array $headers = array(),
        $code = 0
    ) {
        parent::__construct($message, Response::HTTP_INTERNAL_SERVER_ERROR, $detail, $previous, $headers, $code);
    }
}
