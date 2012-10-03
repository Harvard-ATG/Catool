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
	 * User is the currently logged-in user to the application.
	 */
	var user = {
		id: null,
		isModerator: false,
		canModerate: function(user_id) {
			return this.isModerator || this.id == user_id;
		},
		set: function(attr) {
			if(!attr.hasOwnProperty('id') || !attr.hasOwnProperty('isModerator')) {
				throw 'User is missing "id" or "isModerator" attribute';
			}
			this.id = attr.id;
			this.isModerator = attr.isModerator || false;
		}
	};

	return {
		Model: Model,
		View: View,
		Collection: Collection,
		Events: Events,

		user: user,

		models: {},
		views: {},
		collections: {},
		utils:  {} 
	};
})();
