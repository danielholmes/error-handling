<?php

namespace DHolmes\ErrorHandling\Symfony;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

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
        $types = array('notice', 'warning', 'exception', 'fatal', 'error', 'logEmerg', 'logCrit');
        
        $scriptContent = file_get_contents(__DIR__ . '/../Resources/js/handling.js');
        $errorHandlerUrl = $this->urlGenerator->generate('errorHandlingJavaScript');
        
        $content = '<html><head>';
        $content .= '<script type="text/javascript">' . $scriptContent . ' window.onerror = DHolmes.ErrorHandling.createNotifyUrlErrorHandler("' . $errorHandlerUrl . '");</script>';
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
        $content .= '<li><a href="javascript:unknownFunc();return false;">JavaScript Error</a> (Warning, this setup can be quite different to your main site setup)</li>';
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
        
        return new Response('Fatal Error Test, this shouldn\'t show if handling works properly');
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