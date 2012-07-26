<?php

namespace DHolmes\ErrorHandling\Symfony;

use DHolmes\ErrorHandling\ExceptionResponder;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class LogMessageResponder implements ExceptionResponder
{
    /** @var LoggerInterface */
    private $logger;

    /** @param LoggerInterface $logger */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /** @param \Exception $e */
    public function respond(\Exception $e)
    {
        $this->logger->emerg($e);
    }
}