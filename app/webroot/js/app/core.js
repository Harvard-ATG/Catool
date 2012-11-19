// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
/**
 * Top-level namespace for the application.
 *
 * @module Catool 
 * @namespace Catool
 */
Catool = (function() {

	/**
	 * Model class derived from Backbone.Model.
	 *
	 * @class Model
	 * @extends Backbone.Model
	 * @constructor
	 */
	var Model = Backbone.Model.extend({});

	/**
	 * View class derived from Backbone.View.
	 *
	 * @class View
	 * @extends Backbone.View
	 * @constructor
	 */
	var View = Backbone.View.extend({});

	/**
	 * Collection class derived from Backbone.Collection.
	 * 
	 * @class Collection
	 * @extends Backbone.Collection
	 * @constructor
	 */
	var Collection = Backbone.Collection.extend({});

	/**
	 * Events is an application-wide event manager.
	 *
	 * @class Events
	 * @extends Backbone.Events
	 */
	var Events = _.extend({}, Backbone.Events);

	/**
	 * Current user data. 
	 */
	var user = {
		id: null,
		isAdmin: false,
		isModerator: false,
		canModerate: function(user_id) {
			return this.isModerator || this.id == user_id;
		},
		set: function(attr) {
			_.extend(this, attr);
		}
	};

	/**
	 * Settings for the wysihtml5 editor.
	 */
	var wysihtml5Config = {
		"font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
		"emphasis": true, //Italics, bold, etc. Default true
		"lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
		"html": false, //Button which allows you to edit the generated HTML. Default false
		"link": true, //Button to insert a link. Default true
		"image": true, //Button to insert an image. Default true,
		"color": false //Button to change color of font  
	};

	return {
		// classes
		Model: Model,
		View: View,
		Collection: Collection,
		Events: Events,

		// namespaces
		models: {},
		views: {},
		collections: {},
		utils:  {},
		data: {
			wysihtml5Config: wysihtml5Config
		},

		// instances
		user: user
	};
})();
