<?php
/**
 * @author pdziok
 */
namespace ApiProblem;

use Symfony\Component\HttpFoundation\Response;

class NotImplementedYet extends ApiProblemException
{
    public function __construct(
        $message = 'Not implemented yet',
        $detail = null,
        \Exception $previous = null,
        array $headers = array(),
        $code = 0
    ) {
        parent::__construct($message, Response::HTTP_NOT_IMPLEMENTED, $detail, $previous, $headers, $code);
    }
}
