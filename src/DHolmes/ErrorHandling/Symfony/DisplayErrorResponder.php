<?php

namespace DHolmes\ErrorHandling\Symfony;

use Exception;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;

class DisplayErrorResponder implements ExceptionResponder
{
    /** @param Exception $e */
    public function respond(Exception $e)
    {
        if (ini_get('display_errors'))
        {
            if (php_sapi_name() === 'cli')
            {
                echo $e;
            }
            else
            {
                $response = ExceptionHandler::createResponse($e);
                echo $response->getContent();
            }
        }
    }
}