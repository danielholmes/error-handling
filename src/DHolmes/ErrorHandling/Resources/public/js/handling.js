var DHolmes = DHolmes||{};
DHolmes.ErrorHandling = DHolmes.ErrorHandling||{};
DHolmes.ErrorHandling.createNotifyUrlErrorHandler = function(endPointUrl) {
    return function(message, url, lineNumber) {
        //transform errors
        if (typeof(message) === 'object' && message.srcElement && message.target) {
            if (message.srcElement == '[object HTMLScriptElement]' && message.target == '[object HTMLScriptElement]') {
                message = 'Error loading script';
            } else {
                message = 'Event Error - target:' + message.target + ' srcElement:' + message.srcElement;
            }
        }

        message = message.toString();

        // ignore errors
        /*if (message.indexOf("Location.toString") > -1) {
            return;
        }*/
        if (message.indexOf("Error loading script") > -1) {
            return;
        }

        //report errors
        window.onerror = function(){};
        var params = {
            "message": message,
            "scriptUrl": url,
            "lineNumber": lineNumber,
            "url": document.URL,
            "noCache": (new Date()).getTime()
        };
        var queryComps = new Array();
        for (var key in params) {
            queryComps.push(encodeURIComponent(key) + "=" + encodeURIComponent(params[key]));
        }
        (new Image()).src = endPointUrl + "?" + queryComps.join("&");
    };
};