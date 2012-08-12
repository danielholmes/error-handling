<?php

namespace DHolmes\ErrorHandling\Symfony;

use DHolmes\ErrorHandling\ExceptionResponder;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;
use DHolmes\ErrorHandling\DisplayErrorResponder as CoreDisplayErrorResponder;

class DisplayErrorResponder implements CoreDisplayErrorResponder
{
    /** @param Exception $e */
    public function respond(\Exception $e)
    {
        if (ini_get('display_errors'))
        {
            if (php_sapi_name() === 'cli')
            {
                echo $e;
            }
            else
            {
                $handler = new ExceptionHandler();
                $response = $handler->createResponse($e);
                echo $response->getContent();
            }
        }
    }
}