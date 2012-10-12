// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
(function(global) {
	global.Catool = global.Catool || {};

	var View = Backbone.View.extend({});
	var Model = Backbone.Model.extend({});
	var Collection = Backbone.Collection.extend({});

	var InstallView = View.extend({
		template: _.template('<%= message %>'),
		initialize: function(options) {},
		render: function() {}
	});

	global.Catool.InstallView = InstallView;
})(window);
