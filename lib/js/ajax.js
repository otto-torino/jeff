var requestCache = new Array();

/*
 * Ajax requests function
 * performs post and get asynchronous requests
 *
 * Arguments
 * method - (string) The method can be either post or get
 * url - (string) The requested url
 * data - (string) The datas of the request in the form 'var1=value1&var2=value2'
 * target - (mixed) The element DOM Object or the element id of the DOM element that have to be updated with the request response
 *
 * Options (object)
 * cache - (bool default to false) Whether to cache the request result or not
 * cacheTime - (int default 3600000 [1hr]) The time in milliseconds to keep the request in cache
 * load - (mixed default null) The element DOM Object or the element id of the DOM element to use to show the loading image
 * script - (bool default false) True if scripts have to be executed, false if not
 * setvalue - (bool default false) True if script response must be set as the target value
 * callback - (function dafault null) The function to call after the request has been executed
 * callback_params - (string default null) The params passed to the callback function
 *
 * if the called method has to return an error, must return a string like:
 * request error:Error description
 * this way the method is not executed and an alert is displayed with the message "Error description"
 *
 */
function ajaxRequest(method, url, data, target, options) {
	var opt = {
		cache: false,
		cacheTime: 3600000,
		load: null,
		script: false,
		setvalue: false,
		callback: null,
		callback_params: null
	};
	Object.append(opt, options);
	var loading = new Element('div', {'class': 'loading'});
	target = typeof target == 'element'
		? target
		: typeof $(target) != undefined
			? $(target)
			: null;
	if(opt.cache && typeof requestCache[url+data] != undefined && (Date.now - requestCache[url+data][0] < opt.cacheTime)) {
		if(opt.setvalue) target.value = requestCache[url+data][1];
		else target.set('html', requestCache[url+data][1]); 
		return true;
	}

	var opt_load = opt.load != null ? (typeof opt.load == 'element' ? opt.load : $(opt.load)) : null;
	var request = new Request.HTML({
		evalScripts: opt.script,
		url: url,
		method:	method,
		data: data,
		onRequest: function() {
			if(opt_load) loading.inject(opt_load); 
		},
		onComplete: function(responseTree, responseElements, responseHTML, responseJavaScript) {
			if(opt_load) opt_load.set('html', ''); 
			rexp = /request error:(.*)/;
			var err_match = rexp.exec(responseHTML);
			if(err_match) alert(err_match[1]);
			else {
				if(opt.setvalue && target) target.setProperty('value',responseHTML);
				else if(target) target.set('html', responseHTML);
				if(opt.cache) requestCache[url+data] = new Array(Date.now, responseHTML);
				if(opt.callback && opt.callback_params) opt.callback(opt.callback_params);
				else if(opt.callback != null) opt.callback(); 
			}
		}
	}).send();

}

