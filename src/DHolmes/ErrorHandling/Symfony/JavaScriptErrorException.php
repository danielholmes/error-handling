<?php

namespace DHolmes\ErrorHandling\Symfony;

class JavaScriptErrorException extends \Exception
{
    /**
     * @param string $message
     * @param string $scriptUrl
     * @param int $lineNumber
     * @param string $cookie
     * @param string $url
     */
    public function __construct($message, $scriptUrl, $lineNumber, $cookie, $url)
    {
        $messageComps = array(
            'message' => $message,
            'scriptUrl' => $scriptUrl,
            'lineNumber' => $lineNumber,
            'cookie' => $cookie,
            'url' => $url,
        );
        parent::__construct(print_r($messageComps, true));
    }
}