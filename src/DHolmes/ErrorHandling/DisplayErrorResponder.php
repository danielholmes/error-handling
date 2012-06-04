<?php

namespace DHolmes\ErrorHandling;

use Exception;

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
                echo nl2br($e);
            }
        }
    }
}