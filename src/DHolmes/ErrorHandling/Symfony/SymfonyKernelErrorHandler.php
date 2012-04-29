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
        if ($this->isRegistered())
        {
            $this->addDispatcherHandler($dispatcher);
        }
    }
    
    /** @param EventDispatcherInterface $dispatcher */
    private function addDispatcherHandler(EventDispatcherInterface $dispatcher)
    {
        $kernelExceptionHandler = array($this, 'handleKernelException');
        $dispatcher->addListener(KernelEvents::EXCEPTION, $kernelExceptionHandler);
    }
    
    public function register()
    {
        parent::register();
        
        foreach ($this->listeningDispatchers as $dispatcher)
        {
            $this->addDispatcherHandler($dispatcher);
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