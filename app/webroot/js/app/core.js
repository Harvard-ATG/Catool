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
	 * Events class derived from from Backbone.Events.
	 * Can be mixed into any object to priovide custom events.
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
		settings: {},

		// instances
		user: user
	};
})();
