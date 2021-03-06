<?php

namespace DHolmes\ErrorHandling;

class ErrorHandler
{
    /** @var array */
    private static $NON_ERROR_HANDLER_TYPES = array(
        E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING
    );

    /** @var array */
    private $responders;
    /** @var boolean */
    private $registered;

    public function __construct()
    {
        $this->registered = false;
        $this->setResponders(array(new SimpleDisplayErrorResponder()));
    }

    /** @param array $responders */
    public function setResponders(array $responders)
    {
        $this->responders = $responders;
    }

    /** @return array */
    public function getResponders()
    {
        return $this->responders;
    }

    /** @return array */
    private function getNonDisplayResponders()
    {
        return array_values(array_filter($this->responders, function(ExceptionResponder $responder)
        {
            return !($responder instanceof DisplayErrorResponder);
        }));
    }

    /** @param array $responders */
    public function appendResponders(array $responders)
    {
        $this->responders = array_merge($this->responders, $responders);
    }

    /** @param ExceptionResponder $responder */
    public function appendResponder(ExceptionResponder $responder)
    {
        $this->responders[] = $responder;
    }

    /** @return ExceptionResponder */
    public function popResponder()
    {
        return array_pop($this->responders);
    }

    public function clearResponders()
    {
        $this->responders = array();
    }

    public function register()
    {
        $this->registered = true;

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
        if (error_reporting() & $level)
        {
            $e = new \ErrorException($message, 0, $level, $file, $line);
            $this->respondToException($e);
            throw $e;
        }

        return false;
    }

    /** @param \Exception $exception */
    public function handleException(\Exception $exception)
    {
        $this->respondToException($exception);
    }

    /** @param \Exception $exception */
    public function handleExceptionWithoutDisplayResponders(\Exception $exception)
    {
        foreach ($this->getNonDisplayResponders() as $responder)
        {
            $responder->respond($exception);
        }
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
            $e = new \ErrorException($lastError['message'], 0, $lastError['type'], 
                    $lastError['file'], $lastError['line']);
            $this->respondToSystemException($e);
        }
    }

    /** @param \Exception $e */
    protected function respondToSystemException(\Exception $e)
    {
        $this->unregister();
        $this->respondToException($e);
    }

    /** @param \Exception $e */
    private function respondToException(\Exception $e)
    {
        foreach ($this->responders as $responder)
        {
            $responder->respond($e);
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

        $this->registered = false;
    }

    /** @return boolean */
    public function isRegistered()
    {
        return $this->registered;
    }

    /**
     * Used for redirecting error handlers to have no result
     */
    public function nullHandler()
    {
    }

    /**
     * @param array $responders
     * @return ErrorHandler
     */
    public static function registerNew(array $responders = array())
    {
        $handler = new static();
        $handler->appendResponders($responders);
        $handler->register();

        return $handler;
    }
}
