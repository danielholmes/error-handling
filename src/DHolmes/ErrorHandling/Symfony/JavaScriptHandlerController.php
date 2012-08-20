<?php

namespace DHolmes\ErrorHandling\Symfony;

use Symfony\Component\HttpFoundation\Request;

class JavaScriptHandlerController
{
    /** @param Request $request */
    public function logErrorAction(Request $request)
    {
        throw new JavaScriptErrorException($request->request->get('message'),
                    $request->request->get('scriptUrl'), $request->request->get('lineNumber'),
                    $request->request->get('cookie'), $request->request->get('url'));
    }
}