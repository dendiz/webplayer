String.prototype.ellipsis = function(len) {
	if (this.length > len) return this.substr(0,len) + "...";
	return this;
}
Array.prototype.car = function() {
	return this[0];
}
Array.prototype.cdr = function() {
	return this.slice(1-this.length);
}
L = {};
L.default_route = "#!/";
L.routes = [];
L.config = L.config || {
	"viewdir": "views"
};
L.init = function() {
	console.log("limonade init");
	$(window).bind('hashchange', L.hashchange);
	var hash = window.location.hash;
	if (hash.indexOf("#!") < 0) {
		console.log("limonade redirecting to root");
		window.location.href = window.location.href+L.default_route;	
	} else {
		L.hashchange();
	}
}
L.hashchange = function() {
	console.log('hashchanged');
	var routes = L.scan();
	if (routes.length == 0) {
		console.log('no matching routes from',L.routes.length,'total');
		return;
	}
	console.log("matching routes are", routes);
	var route = routes.car();
	//TODO: get the return value from the routed function to display the view.
	var ret = route.fn.apply(window, route.params);

}
L.html = function(el, name, data) {
	$(el).html(L.interpolate(name, data));
}

L.interpolate= function(name, data) {
	var $el = $(name);
	if ($el.size() == 0) {
		console.error('template', name, 'was not found. Did you create the script element to hold it?');
		return;
	}
	return L.tmpl($el.html(),data);
}
L.tmpl = function(str, data){
	// Figure out if we're getting a template, or if we need to
	// load the template - and be sure to cache the result.
	var fn = 
	  new Function("obj",
		"var p=[],print=function(){p.push.apply(p,arguments);};" +

		// Introduce the data as local variables using with(){}
		"with(obj){p.push('" +

		// Convert the template into pure JavaScript
		str
		  .replace(/[\r\t\n]/g, " ")
		  .split("<%").join("\t")
		  .replace(/((^|%>)[^\t]*)'/g, "$1\r")
		  .replace(/\t=(.*?)%>/g, "',$1,'")
		  .split("\t").join("');")
		  .split("%>").join("p.push('")
		  .split("\r").join("\\'")
	  + "');}return p.join('');");

	// Provide some basic currying to the user
	return data ? fn( data ) : fn;
}	
L.scan = function() {
	var url = window.location.hash.replace("#!","");
	if (url.substr(-1) != "/") url += "/";
	console.log("scanning routes for", url);
	var routes = $.grep(L.routes, function(route) {
		console.log("..matching", route.pattern, "to", url);
		var reg = new RegExp(route.pattern);
		return reg.test(url);
	});
	$.each(routes, function(i,it) {
		var reg = new RegExp(it.pattern);
		var matches = reg.exec(url);
		if (matches) {
			it.params = matches.cdr();
			console.log('..binding params', it.params);
		}
	});
	return routes;
}
$(document).ready(function() {
	L.init();
});
L.dispatch = function(route, fn) {
	if (route.substr(-1) != "/") route += "/";
	var segs = $.grep(route.split("/"), function(it) {return it != ""});
	var params = [];
	var pattern = "/";
	$.each(segs, function(i,it) {
		if (it[0] == ':') {
			pattern += "([^/]+)/";	
			matches = /^:([^\:]+)$/.exec(it);
			if (matches) {
				params.push(matches.cdr().car());
			}
		} else {
			pattern += it+'/'; 
		}
	});
	L.routes.push({route:route,fn:fn,pattern:pattern, params: params});
	console.log("registering route",route," - registered routes", L.routes);
}
