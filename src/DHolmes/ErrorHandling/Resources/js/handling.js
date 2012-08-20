var DHolmes = DHolmes||{};
DHolmes.ErrorHandling = DHolmes.ErrorHandling||{};
DHolmes.ErrorHandling.createNotifyUrlErrorHandler = function(endPointUrl) {
	return function(message, url, lineNumber) {
		var request = null;
		if (window.ActiveXObject) { // IE
			request = new ActiveXObject("Microsoft.XMLHTTP");
		} else {
			request = new XMLHttpRequest();
		}
		request.open('POST', endPointUrl, true);
		request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		var params = {
			"message": message,
			"scriptUrl": url,
			"lineNumber": lineNumber,
			"cookie": document.cookie,
			"url": document.URL
		};
		var queryComps = new Array();
		for (var key in params) {
			queryComps.push(encodeURIComponent(key) + "=" + encodeURIComponent(params[key]));
		}
		request.send(queryComps.join("&"));
	};
};