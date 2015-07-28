<?php
/**
 * @author pdziok
 */
namespace ApiProblem;

use Symfony\Component\HttpFoundation\Response;

class NotFound extends ApiProblemException
{
    public function __construct(
        $message,
        $detail = null,
        \Exception $previous = null,
        array $headers = array(),
        $code = 0
    ) {
        parent::__construct($message, Response::HTTP_NOT_FOUND, $detail, $previous, $headers, $code);
    }
}
