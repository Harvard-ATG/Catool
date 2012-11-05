// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
(function(App) {
	// dependencies
	var Model = App.Model;
	var Collection = App.Collection;
	var url = App.utils.url;
	
	/**
	 * Segment model.
	 *
	 * @class Segment
	 * @namespace Catool.models
	 * @extends Catool.Model
	 * @constructor
	 */
	var Segment = Model.extend({
		idAttribute: 'id'
	});
	App.models.Segment = Segment;

	/**
	 * Segments collection.
	 *
	 * @class Segments
	 * @namespace Catool.collections
	 * @extends Catool.Collection
	 * @constructor
	 */
	var Segments = Collection.extend({
		/**
		 * Associate this collection with Segment models.
		 *
		 * @property model
		 */
		model: Segment 
	});
	App.collections.Segments = Segments;

	/**
	 * Tag model.
	 *
	 * @class Tag
	 * @namespace Catool.models
	 * @extends Catool.Model
	 * @constructor
	 */
	var Tag = Model.extend({
		idAttribute: 'id'
	});
	App.models.Tag = Tag;

	/**
	 * Tags collection.
	 *
	 * @class Tags
	 * @namespace Catool.collections
	 * @extends Catool.Collection
	 * @constructor
	 */
	var Tags = Collection.extend({
		/**
		 * Associate this collection with Segment models.
		 *
		 * @property model
		 */
		model: Tag 
	});
	App.collections.Tag = Tags;

	
	/**
	 * User model.
	 *
	 * @class User
	 * @namespace Catool.models
	 * @extends Catool.Model
	 * @constructor
	 */
	var User = Model.extend({
		idAttribute: 'id',

		/**
		 * Initialize the user model.
		 *
		 * Overridden to automatically create a *fullname* attribute if
		 * it's not provided.
		 *
		 * @method initialize
		 */
		initialize: function() {
			if(!this.get('fullname')) {
				this.set({ fullname: this.get("name") });
			}
		}
	});
	App.models.User  = User;

	/**
	 * Note model.
	 *
	 * @class Note
	 * @namespace Catool.models
	 * @extends Catool.Model
	 * @constructor
	 */
	var Note = App.Model.extend({
		idAttribute: 'id',

		/**
		 * Defines the url root for updates to the model.
		 *
		 * @property urlRoot
		 * @default /notes
		 */
		urlRoot: url('/notes'),

		/**
		 * Initialize the note model.
		 */
		initialize: function() {
		},

		/**
		 * Parse the server response.
		 *
		 * Overridden to parse the CakePHP response and associate 
		 * the note with its related model instances.
		 *
		 * @method parse
		 * @param {Object} response
		 * @return {Object} model attributes
		 */
		parse: function(response) {
			var attrs = {};
			
			if(response.results) {
				response = response.results;
			}

			_.extend(attrs, response.Note);
			if(response.User) {
				attrs.user = new User(response.User);
			}
			if(response.Segment) {
				attrs.segments = new Segments(response.Segment);
			}
			if(response.Tag) {
				attrs.tags = new Tags(response.Tag);
			}
			
			return attrs;
		},

		/**
		 * Test if the Note is an annotation.
		 *
		 * @method isAnnotation
		 * @return {Boolean} true if it is an annotation, false otherwise.
		 */
		isAnnotation: function() {
			return this.get('type') === 'annotation';
		},

		/**
		 * Test if the Note is a comment.
		 *
		 * @method isComment
		 * @return {Boolean} true if it is a comment, false otherwise.
		 */
		isComment: function() {
			return this.get('type') === 'comment';
		},

		/**
		 * Test if a note is a comment on another note.
		 *
		 * @method isCommentOn
		 * @param {Object} note An instance of Note 
		 * @return {Boolean} true if it is a comment on the note, false otherwise.
		 */
		isCommentOn: function(note) {
			return this.isComment() && this.get('parent_id') === note.get('id'); 
		},

		/**
		 * Retrieve all comments on this.
		 *
		 * @method getComments 
		 * @return {Object} list of comments on the annotation
		 */
		getComments: function() {
			if(this.isAnnotation()) {
				return this.collection.getCommentsFor(this);
			}
			return null;
		},

		/**
		 * Checks if the model has any tags.
		 */
		hasTags: function() {
			return this.get('tags').size() > 0;
		}
	});
	App.models.Note = Note;

	/**
	 * Notes collection.
	 *
	 * @class Notes
	 * @namespace Catool.collections
	 * @extends Catool.Collection
	 * @constructor
	 */
	var Notes =  Collection.extend({
		/**
		 * Associate this collection with Note models.
		 *
		 * @property model
		 */
		model: Note,

		/**
		 * An instance of a target model (i.e. Video, etc). This is 
		 * required to fetch the default set of models from the server.
		 *
		 * @property targetModel
		 */
		targetModel: null,

		/**
		 * Initialize the collection.
		 *
		 * Note: this is called by the constructor.
		 *
		 * @method initialize
		 * @param {Array} models An array of models
		 * @param {Object} options A set of config options.
		 */
		initialize: function(models, options) {
			this.targetModel = options.targetModel;
		},

		/**
		 * Parse the server response.
		 *
		 * @method parse
		 * @param {Object} response Response object
		 * @return {Array} An array of results
		 */
		parse: function(response) {
			return response.results;
		},

		/**
		 * Fetch the default set of models from the server. Overrides 
		 * the default fetch method to limit the models to the current
		 * target.
		 *
		 * @method fetch
		 * @param {Object} options A set of options
		 * @return a set of models
		 */
		fetch: function(options) {
			var success, data;
			options = options || {};
			success = options.success;
			data = options.data;

			options.url = url('/notes?target_id=' + this.targetModel.get('id'));
			options.success = _.bind(function() {
				if(success) {
					success.apply(this, arguments);
				}
				this.trigger('fetch', this, data);
			}, this);

			return Collection.prototype.fetch.call(this, options);
		},

		/**
		 * Search the collection
		 * 
		 * @method search
		 * @param {Object} a query object
		 * @return a set of models
		 */
		 search: function(query) {
			this.fetch({ data: query });
		 },

		/**
		 * Returns the comments for an annotation.
		 *
		 * @method getCommentsFor
		 * @param {Object} annotation An instance of a Note model
		 * @return {Array} a list of Note models
		 */
		getCommentsFor: function(annotation) {
			var comments =_(this.models).filter(function(note) {
				return note.isCommentOn(annotation);
			});
			
			return comments;
		},

		/**
		 * Returns a list of annotations. 
		 *
		 * @method getAnnotations
		 * @return {Array} a list of annotation models
		 */
		getAnnotations: function() {
			return this.filter(function(note) {
				return note.isAnnotation();
			});
		}
	});
	App.collections.Notes = Notes;

	/**
	 * Video target model.
	 *
	 * @class Video
	 * @namespace Catool.models
	 * @extends Catool.Model
	 * @constructor
	 */
	var Video = Model.extend({
		idAttribute: 'id',

		/**
		 *  Defines the url root for updates to the model.
		 *
		 * @property urlRoot
		 * @default /videos
		 */
		urlRoot: url('/videos')
	});
	App.models.Video = Video;

})(Catool);
