<?php

namespace DHolmes\ErrorHandling\Symfony;

use DHolmes\ErrorHandling\ErrorHandler;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class SymfonyKernelErrorHandler extends ErrorHandler
{
    /** @var array */
    private $listeningDispatchers = array();
    
    /** @param EventDispatcherInterface $dispatcher */
    public function listenToKernelErrors(EventDispatcherInterface $dispatcher)
    {
        $this->listeningDispatchers[] = $dispatcher;
    }
    
    public function register()
    {
        parent::register();
        
        $kernelExceptionHandler = array($this, 'handleKernelException');
        foreach ($this->listeningDispatchers as $dispatcher)
        {
            $dispatcher->addListener(KernelEvents::EXCEPTION, $kernelExceptionHandler);
        }
    }
    
    /** @param GetResponseForExceptionEvent $event */
    public function handleKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if (!($exception instanceof NotFoundHttpException) && 
            !($exception instanceof AccessDeniedException) &&
            !($exception instanceof AuthenticationCredentialsNotFoundException))
        {
            $this->respondToException($exception);
        }
    }
    
    /** @inheritDoc */
    protected function unregisterHandling()
    {
        parent::unregisterHandling();
        
        $kernelHandler = array($this, 'handleKernelException');
        foreach ($this->listeningDispatchers as $dispatcher)
        {
            $dispatcher->removeListener(KernelEvents::EXCEPTION, $kernelHandler);
        }
    }
}