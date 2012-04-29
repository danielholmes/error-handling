<?php

namespace DHolmes\ErrorHandling\Symfony;

use Symfony\Component\HttpFoundation\Response;

class TestController
{
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