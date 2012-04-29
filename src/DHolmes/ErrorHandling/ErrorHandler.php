<?php

namespace DHolmes\ErrorHandling;

use Exception;
use ErrorException;

class ErrorHandler
{
    /** @var array */
    private static $NON_ERROR_HANDLER_TYPES = array(
        E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING
    );
    
    /** @var ExceptionResponder */
    private $responder;
    
    /** @param ExceptionResponder $responder */
    public function __construct(ExceptionResponder $responder = null)
    {
        $this->responder = $responder;
    }
    
    /** @param ExceptionResponder $responder */
    public function setResponder(ExceptionResponder $responder)
    {
        $this->responder = $responder;
    }
    
    public function register()
    {
        set_error_handler(array($this, 'handleError'));
        register_shutdown_function(array($this, 'handleShutdown'));
        set_exception_handler(array($this, 'handleException'));
    }
    
    /**
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array $context
     * @return boolean 
     */
    public function handleError($level, $message, $file, $line, $context)
    {
        if (error_reporting() & $level) {
            $e = new ErrorException($message, 0, $level, $file, $line);
            $this->respondToException($e);
            throw $e;
        }

        return false;
    }
    
    /** @param Exception $exception */
    public function handleException(Exception $exception)
    {
        $this->respondToException($exception);
    }
    
    /**
     * @param int $type
     * @return boolean
     */
    private function isNonErrorHandlerType($type)
    {
        return in_array($type, self::$NON_ERROR_HANDLER_TYPES, true);
    }
    
    public function handleShutdown()
    {
        $lastError = error_get_last();
        if ($lastError !== null && isset($lastError['type']) && 
            $this->isNonErrorHandlerType($lastError['type']))
        {
            // Can't send to handle because cant throw and handle exception during shutdown
            $e = new ErrorException($lastError['message'], 0, $lastError['type'], 
                    $lastError['file'], $lastError['line']);
            $this->respondToException($e);
        }
    }
    
    /** @param Exception $e */
    protected function respondToException(Exception $e)
    {
        $this->unregister();
        if ($this->responder !== null)
        {
            $this->responder->respond($e);
        }
    }
    
    /**
     * Unregister all handlers so don't get stuck in loops and the like 
     */
    public function unregister()
    {
        register_shutdown_function(array($this, 'nullHandler'));
        set_error_handler(array($this, 'nullHandler'));
        set_exception_handler(array($this, 'nullHandler'));
    }
    
    /**
     * Used for redirecting error handlers to have no result
     */
    public function nullHandler()
    {
        
    }
    
    /**
     * @param ExceptionResponder $responder
     * @return ErrorHandler
     */
    public static function registerNew(ExceptionResponder $responder = null)
    {
        $handler = new static($responder);
        $handler->register();
        
        return $handler;
    }
}