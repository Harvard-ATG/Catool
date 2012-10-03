(function() {
	var script = Catool.script;

	module("script");
	test("set a script", function() {
		var id = 'first-script-name';
		var callback = function() { 
			return true; 
		};

		ok(!script(id), "script undefined");
		script(id, callback);
		equal(script(id), callback, "script returns a callback");
	});
})();
