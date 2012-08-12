<?php

namespace DHolmes\ErrorHandling;

interface ExceptionResponder
{
    /** @param \Exception $e */
    public function respond(\Exception $e);
}