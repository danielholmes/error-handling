<?php

namespace DHolmes\ErrorHandling;

use Exception;

interface ExceptionResponder
{
    /** @param Exception $e */
    public function respond(Exception $e);
}