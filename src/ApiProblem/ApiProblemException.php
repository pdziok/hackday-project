<?php

namespace ApiProblem;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiProblemException extends HttpException
{
    /**
     * Error detailed information
     * @var string
     */
    private $detail;

    /**
     * @param string     $message    Exception message
     * @param int        $statusCode HTTP status code
     * @param string     $detail     Detailed error information
     * @param \Exception $previous   optional previous exception
     * @param array      $headers    optional headers
     * @param int        $code       exception error code
     */
    public function __construct(
        $message,
        $statusCode,
        $detail = null,
        \Exception $previous = null,
        array $headers = array(),
        $code = 0
    ) {
        parent::__construct($statusCode, $message, $previous, $headers, $code);

        $this->detail = $detail;
    }

    /**
     * Get api problem title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getMessage();
    }

    /**
     * Get api problem status code
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->getStatusCode();
    }

    /**
     * Get api problem detailed informatino
     *
     * @return string
     */
    public function getDetail()
    {
        return $this->detail;
    }
}
