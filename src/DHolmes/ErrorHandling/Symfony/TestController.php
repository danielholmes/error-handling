<?php

namespace DHolmes\ErrorHandling\Symfony;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class TestController
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var LoggerInterface */
    private $logger;
    
    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param LoggerInterface $logger 
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, LoggerInterface $logger)
    {
        $this->urlGenerator = $urlGenerator;
        $this->logger = $logger;
    }
    
    /** @return Response */
    public function indexAction()
    {
        $types = array(
            'notice',
            'warning',
            'exception',
            'fatal',
            'fatalMemory',
            'error',
            'logEmerg',
            'logCrit'
        );
        $scriptContent = file_get_contents(__DIR__ . '/../Resources/public/js/handling.js');
        $errorHandlerUrl = null;
        try
        {
            $errorHandlerUrl = $this->urlGenerator->generate('errorHandlingJavaScript');
        }
        catch (RouteNotFoundException $e)
        {
            // will check on error handler
        }

        $content = '<html><head>';
        if ($errorHandlerUrl !== null)
        {
            $content .= '<script type="text/javascript">' . $scriptContent . ' window.onerror = DHolmes.ErrorHandling.createNotifyUrlErrorHandler("' . $errorHandlerUrl . '");</script>';
        }
        $content .= '</head><body>';
        $content .= '<h3>Error Testing</h3>';
        $content .= '<ul>';
        foreach ($types as $type)
        {
            $url = $this->urlGenerator->generate(sprintf('errorTest%s', ucfirst($type)));
            $content .= '<li>' . strtoupper($type) . ': ';
            $content .= sprintf('<a href="%s">GET</a>', $url) . ' | ';
            $content .= sprintf('<form action="%s" method="post" style="display:inline">
                                    <input type="submit" value="POST" />
                                </form>', $url);
            $content .= '</li>';
        }
        if ($errorHandlerUrl !== null)
        {
            $content .= '<li><a href="javascript:testJSErrorHandling();return false;">JavaScript Error</a> (Warning, this setup can be quite different to your main site setup)</li>';
        }
        $content .= '</ul>';
        $content .= '</body</html>';
        return new Response($content);
    }

    public function exceptionAction()
    {
        throw new \Exception('Testing Exception');
    }

    /** @return Response */
    public function fatalAction()
    {
        $var->hello();

        return new Response('Fatal Error Test, this won\'t show if handling works properly');
    }

    /** @return Response */
    public function fatalMemoryAction()
    {
        $holder = '';
        while (true)
        {
            $holder .= str_repeat('0123456789', 9999999);
        }
        
        return new Response('Fatal Memory Error Test, this won\'t show if handling works properly');
    }

    /** @return Response */
    public function errorAction()
    {
        $var = null;
        $var->hello();
        
        return new Response('Error Test, this shouldn\'t show if handling works properly');
    }

    /** @return Response */
    public function warningAction()
    {
        strpos();
        
        return new Response('Warning Test, this shouldn\'t show if handling works properly');
    }

    /** @return Response */
    public function noticeAction()
    {
        $arr = array();
        $var = $arr['unknown'];
        
        return new Response('Notice Test, this shouldn\'t show if handling works properly');
    }
    
    /** @return Response */
    public function logCritAction()
    {
        $message = 'Testing Crit Message';
        $this->logger->crit($message);
        return new Response('Logged crit: ' . $message);
    }
    
    /** @return Response */
    public function logEmergAction()
    {
        $message = 'Testing Emerg Message';
        $this->logger->emerg($message);
        return new Response('Logged emerg: ' . $message);
    }
}
