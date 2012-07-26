<?php

namespace DHolmes\ErrorHandling;

class OperationQuarantine
{
    /** @var array */
    private $responders;
    
    /** @param array $responders */
    public function __construct(array $responders = array())
    {
        $this->responders = $responders;
    }
    
    /** @var ExceptionResponder $responder */
    public function appendResponder(ExceptionResponder $responder)
    {
        $this->responders[] = $responder;
    }

    /**
     * @param \Closure $operation
     * @param \Closure $failOperation
     * @return mixed
     */
    public function run(\Closure $operation, \Closure $failOperation = null)
    {
        $result = null;
        try
        {
            $result = $operation();
        }
        catch (\Exception $e)
        {
            foreach ($this->responders as $responder)
            {
                $responder->respond($e);
            }
            
            if ($failOperation !== null)
            {
                $result = $failOperation();
            }
        }
        return $result;
    }
}