<?php

namespace DHolmes\ErrorHandling;

class SimpleDisplayErrorResponder implements DisplayErrorResponder
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
                echo nl2br($e);
            }
        }
    }
}