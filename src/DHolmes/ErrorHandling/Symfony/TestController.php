<?php

namespace DHolmes\ErrorHandling\Symfony;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TestController
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    
    /** @param UrlGeneratorInterface $urlGenerator */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }
    
    /** @return Response */
    public function indexAction()
    {
        $types = array('notice', 'warning', 'exception', 'fatal', 'error');
        $content = '<h3>Error Testing</h3>';
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
        $content .= '</ul>';
        return new Response($content);
    }
    
    /** @return Response */
    public function exceptionAction()
    {
        throw new \Exception('Testing Exception');
        
        return new Response('Exception Test');
    }

    /** @return Response */
    public function fatalAction()
    {
        $var->hello();
        
        return new Response('Fatal Error Test');
    }

    /** @return Response */
    public function errorAction()
    {
        $var = null;
        $var->hello();
        
        return new Response('Error Test');
    }

    /** @return Response */
    public function warningAction()
    {
        strpos();
        
        return new Response('Warning Test');
    }

    /** @return Response */
    public function noticeAction()
    {
        $arr = array();
        $var = $arr['unknown'];
        
        return new Response('Notice Test');
    }
}