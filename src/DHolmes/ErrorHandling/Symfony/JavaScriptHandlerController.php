<?php

namespace DHolmes\ErrorHandling\Symfony;

use Symfony\Component\HttpFoundation\Request;

class JavaScriptHandlerController
{
    /** @param Request $request */
    public function logErrorAction(Request $request)
    {
        throw new JavaScriptErrorException($request->query->get('message'),
                    $request->query->get('scriptUrl'), $request->query->get('lineNumber'),
                    $request->query->get('url'));
    }
}