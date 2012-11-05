// @file app/core.js
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
// @file app/utils.js
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
(function(App) {

	/**
	 * A simple class for converting time values from strings
	 * to seconds and vice versa.
	 *
	 * @example
	 * 		var t = new Catool.utils.TimeConverter(9187);
	 * 		if(t.isValid()) {
	 *			console.log(t.toString());
	 *		}
	 * 
	 * Or alternatively:
	 *
	 * @example
	 * 		Catool.utils.TimeConverter(314).toString(); // "0:05:14"
	 * 		Catool.utils.TimeConverter("3:14:15").toSeconds(); // 11655
	 *
	 * @class TimeConverter
	 * @namespace Catool.utils
	 * @constructor
	 */
	var TimeConverter = function(time) {
		if(!(this instanceof TimeConverter)) {
			return new TimeConverter(time);
		}
		
		this.time = time;
		this._parsed = false;
		this._value = {};  // holds parsed representation
		return this;
	};
	
	/**
	 * Invalid text
	 *
	 * @property invalidText
	 * @type String
	 * @static
	 */
	TimeConverter.invalidText = 'Invalid format. Must be HH:MM:SS';
	
	TimeConverter.prototype = {
		/**
		 * Numeric pattern.
		 *
		 * @private
		 * @property numRe
		 * @type RegExp
		 */
		numRe: /^(\d+)$/,

		/**
		 * Numeric pattern parts. This is corresponds to the numeric
		 * pattern matches.
		 *
		 * @private
		 * @property numParts
		 * @type Array
		 */
		numParts: ['seconds'],

		/**
		 * String pattern.
		 *
		 * @private
		 * @property formatRe
		 * @type RegExp
		 */
		formatRe: /^(?:(\d{1,3}):)?(\d{1,2}):(\d{2})(?:[.,](\d+))?$/, // Format: [hh:]mm:ss[.s]

		/**
		 * Matched parts of the string pattern.
		 *
		 * @private
		 * @property formatParts
		 * @type Array
		 */
		formatParts: ['hours', 'minutes', 'seconds'],
		
		/**
		 * Validate the time.
		 *
		 * @method isValid
		 * @return {Boolean} Returns true if the time is valid, false otherwise.
		 */
		isValid: function() {
			 return this.numRe.test(this.time) || this.formatRe.test(this.time);
		},

		/**
		 * Parses the time. 
		 *
		 * @method parse
		 */
		parse: function() {
			if(typeof this.time === 'number' || this.numRe.test(this.time)) {
				this._parseNumber(Number(this.time));
			} else if(typeof this.time === 'string') {
				this._parseString(this.time);
			}
		},

		/**
		 * Helper method to convert the time in seconds to h/m/s.
		 *
		 * @private
		 * @method _parseNumber
		 * @param {Number} t time in seconds
		 */
		_parseNumber: function(t) {
			var h, m, s;
			h = Math.floor(t / 3600);
			t -= h * 3600;
			m = Math.floor(t / 60);
			t -= m * 60;
			s = Math.floor(t);
			
			this._value = {
				hours: h,
				minutes: m,
				seconds: s
			};
			this._parsed = true;
		},

		/**
		 * Helper method to parse a time string into its component parst.
		 *
		 * @private
		 * @method _parseString
		 * @param {String} t time string 
		 */
		_parseString: function(t) {
			var parts = this.formatParts;
			var match = this.formatRe.exec(t);
			
			if(match) {
				match.shift(); // discard matched text
				for (var i = 0, len = parts.length; i < len; i++ ) {
					this._value[parts[i]] = Number(match[i] || 0);
				}
				this._parsed = true;
			}
		},

		/**
		 * Helper method to automatically parse the time before
		 * doing some work.
		 *
		 * @private
		 * @method _parseAndDo
		 * @param {Function} callback A function to be executed.
		 * @return The result of calling the function, or null if the time is invalid.
		 */
		_parseAndDo: function(callback) {
			if(!this._parsed) {
				if(!this.isValid()) {
					return null;
				}
				this.parse();
			}
			return callback.call(this, this._value);
		},

		/**
		 * Returns the time in seconds.
		 *
		 * @method toSeconds
		 * @return {Number} Time in seconds
		 */
		toSeconds: function() {
			return this._parseAndDo(function(t) {
				return (t.hours * 3600) + (t.minutes * 60) + t.seconds;				
			});
		},

		/**
		 * Returns the time formatted as a string h:mm:ss.
		 *
		 * @method toString
		 * @return {String} Time formatted as a string.
		 */
		toString: function() {
			var lpad = function(v) {
				var str = v ? v+'' : '';
				while(str.length < 2) {
					str = '0' + str;
				}
				return str;
			};
			return this._parseAndDo(function(t) {
				var parts = [ t.hours||'0', lpad(t.minutes), lpad(t.seconds) ];
				return parts.join(':');
			});
		}
	};
	
	/**
	 * A class for synchronizing notes with the video player and vice versa.
	 *
	 * It can be used to keep notes in sync with the video by means of a set of
	 * "cues" (events raised by the player at specific times) or to request
	 * that a note segment is played from start to end.
	 *
	 * @class NoteSyncModel
	 * @namespace Catool.utils
	 * @constructor
	 */
	var NoteSyncModel = function(options) {
		options = options || {};

		this.player = options.player;
	};

	NoteSyncModel.prototype = {

		/**
		 * Template for displaying empty text.
		 *
		 * @property emptyTemplate
		 * @type String
		 */
		emptyTemplate: _.template('<div class="notes-empty">No annotations to display at this time (<%= total %> total).</div>'),

		/**
		 * Initialize the sync model.
		 *
		 * @method initialize
		 * @param {Object} view the container that is being synced
		 * @return undefined
		 */
		initialize: function(view) {
			this.container = view;
			this.collection = view.collection;
			this.lastCue = -1;
			
			this.container.on('toggleSync', this._onToggleSync, this);
			this.container.on('playNote', this._onPlayNote, this);
			
			if(view.syncEnabled) {
				this.player.on('play', _.once(this.enableSync), this);
			}
		},

		/**
		 * Enable synchronization.
		 *
		 * @method enableSync
		 * @return undefined
		 */
		enableSync: function() {
			this.syncEnabled = true;
			this._updateCuePoints();
			this.container.sortBy('start-time', 'desc');
			this.collection.on('add remove change fetch', this._updateCuePoints, this);
			this.player.on('cue cueready', this._showViewsAtCue, this);
		},

		/**
		 * Disable synchronization.
		 *
		 * @method disableSync
		 * @return undefined
		 */
		disableSync: function() {
			this.syncEnabled = false;
			this.player.off('cue cueready', this._showViewsAtCue, this);
			this.collection.off('add remove change fetch', this._updateCuePoints, this);
			this._clearCuePoints();
			this._resetContainer();
		},

		/**
		 * Reset the container. 
		 *
		 * @method _resetContainer
		 * @protected
		 */
		_resetContainer: function() {
			this._removeEmptyText();
			_.each(this.container.views, function(view) {
				view.show();
			});
		},
		
		/**
		 * Shows views up to the cue point.
		 *
		 * @method _showViewsAtCue
		 * @param {Object} player the video player view
		 * @param {Number} playerTime the time when the cue was triggered
		 * @param {Number} cueTime the cueTime (cueTime <= playerTime)
		 * @protected
		 */
		_showViewsAtCue: function(player, playerTime, cueTime) {
			var views = this.container.views;
			var num_views = views.length;
			var num_hidden = 0;
			var i, view, start_time;
			
			for(i = 0; i < num_views; i++) {
				view = views[i];
				start_time = parseInt(view.model.get('segments').first().get('start_time'), 10);
				if(start_time < cueTime) {
					view.collapse().show();
				} else if(start_time == cueTime) {
					view.expand().show();
				} else {
					view.hide();
					num_hidden++;
				}
			}

			this._removeEmptyText();
			if(num_views === num_hidden) {
				this._showEmptyText(num_views);
			}

			this.lastCue = cueTime;
		},

		/**
		 * Show empty text if there are no notes to display.
		 *
		 * @method _showEmptyText
		 * @protected
		 */
		_showEmptyText: function(total) {
			this.container.$el.append(this.emptyTemplate({ total: total }));
		},

		/**
		 * Removes empty text from the view.
		 *
		 * @method _removeEmptyText
		 * @protected
		 */
		_removeEmptyText: function() {
			this.container.$('.notes-empty').remove();
		},

		/**
		 * Handles sync toggle button event from container view.
		 *
		 * @method _onToggleSync
		 * @protected
		 */
		_onToggleSync: function(state) {
			this[state?'enableSync':'disableSync']();
		},

		/**
		 * Handles the event triggered by clicking the play button on notes.
		 *
		 * @method _onPlayNote
		 * @param {Object} model the note model
		 * @param {Object} view the view that triggered the play event
		 * @protected
		 */
		_onPlayNote: function(model, view) {
			var segments = model.get('segments');
			var segment = segments.first();
			var start = TimeConverter(segment.get('start_time')).toSeconds();
			var end = TimeConverter(segment.get('end_time')).toSeconds();

			if(start !== null && end !== null) {
				this.player.playSegment(start, end);
			}
		},

		/**
		 * Updates the cue points in the player. This is typically
		 * called when the collection changes.
		 *
		 * @method _updateCuePoints
		 * @protected
		 */
		_updateCuePoints: function() {
			this.cues = this._getCuePoints();
			this.player.cue(this.cues, { reset: true });
			if(this.lastCue >= 0) {
				this._showViewsAtCue(null, this.lastCue, this.lastCue);
			}
		},

		/**
		 * Removes or clears the cue points in the player.
		 *
		 * @method _clearCuePoints
		 * @protected
		 */
		_clearCuePoints: function() {
			this.player.cue([], { reset: true });
			this.lastCue = -1;
		},

		/**
		 * Retrieves a sorted list of unique cue points (i.e. note start times).
		 *
		 * @method _getCuePoints
		 * @return {Array} a list of start times (seconds)
		 */
		_getCuePoints: function() {
			var points = _(this.collection.getAnnotations()).map(function(note) {
				var segment = note.get('segments').first();
				return parseInt(segment.get('start_time'), 10);
			});
			
			return _.uniq(points).sort(function(a, b) {
				return a - b;	
			});
		}
	};

	App.utils = _.extend(App.utils, {
			/**
			 * Constructs a url back to the application.
			 *
			 * @method url
			 * @param {String} value A URL
			 * @return {String} Returns a URL
			 * @static
			 */
			url: function(value) { return value; },

			/**
			 * Converts newlines to HTML breaks.
			 *
			 * @method nl2br
			 * @param {String} str Text
			 * @param {Boolean} is_xhtml Enable XHTML output
			 * @return {String} Returns the text with newlines converted to break tags
			 * @static
			 */
			nl2br: function  (str, is_xhtml) {   
				var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';    
				return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
			},

			TimeConverter: TimeConverter,
			NoteSyncModel: NoteSyncModel
	});

})(Catool);

// @file app/models.js
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
// @file app/views.js
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.

/**
 * A class for creating basic alert messages. 
 *
 * The alert is created with twitter bootstrap markup and 
 * the is dismissable using the jQuery *alert* plugin.
 *
 * This is only meant for simple messages. Consider writing 
 * custom Underscore templates for anything more complex.
 * 
 * @class AlertView
 * @namespace Catool.views
 * @constructor
 */
(function(App) {
	// dependencies
	var View = App.View;
	
	var alerts = ['success', 'error', 'info'];
	
	var AlertView = View.extend({

		/**
		 * Class name for the container div element.
		 *
		 * @property className
		 * @default alert fade in
		 */
		className: 'alert fade in',

		/**
		 * Template for the alert markup.
		 *
		 * @property template
		 */
		template: _.template([
			'<a class="close" data-dismiss="alert">&times;</a>',
			'<%= message %>'
		].join('')),

		/**
		 * Valid alert types.
		 *
		 * @property alerts
		 */
		alerts: alerts,

		/**
		 * Initialize the view.
		 *
		 * @method initialize
		 * @param {Object} options Hash of options. Requires a *type* and *msg* parameter. The type can be one of *success*, *error*, or *info*.
		 */
		initialize: function(options) {
			var message = options.msg || '';
			var alert = options.hasOwnProperty('alert') ? options.alert : null;
			
			if(alert !== null && _.indexOf(this.alerts, alert) === -1) {
				throw new Error('Invalid alert: [' + alert + '] Must be one of: ' + this.alerts.join(', '));
			}

			this.alert = alert;
			this.message = message;
		},

		/**
		 * Render the alert to the dom and wire up the close button.
		 *
		 * @method render
		 * @return {Object} this
		 */
		render: function() {
			var output = this.template({ message: this.message });
			this.$el.addClass(this.alert === null ? '' : 'alert-'+this.alert).
				html(output).
				alert(); // jquery plugin
			return this;
		},

		/**
		 * Close an alert.
		 *
		 * @method close
		 */
		close: function() {
			this.$el.alert('close');
		}
	});
	
	/**
	 * Convenience method for creating a message. 
	 *
	 * @method msg
	 * @param {Object} options Alert Options passed to the constructor.
	 * @return {Object} an AlertView instance
	 * @static
	 * @example
	 *		$("#error").html(AlertView.msg({ type: "error", msg: "Oops!" }).el);
	 *
	 * @example 
	 *		AlertView.msg({ type: "success", msg: "Good job!", applyTo: $('#myalert') });
	 */
	AlertView.msg = function(options) {
		var alert, applyTo;

		if(options.hasOwnProperty('applyTo')) {
			applyTo = options.applyTo;
			delete options.applyTo;
		}

		alert = new AlertView(options);
		alert.render();

		if(typeof applyTo !== 'undefined') {
			applyTo.html(alert.el);
		}

		return alert;
	};

	/**
	 * Convenience method for creating an error message.
	 *
	 * @method error
	 * @param {String} msg An error message
	 * @return {Object} an AlertView instance
	 * @static
	 */

	/**
	 * Convenience method for creating an info message.
	 *
	 * @method info
	 * @param {String} msg An info message 
	 * @return {Object} an AlertView instance
	 * @static
	 */
	
	/**
	 * Convenience method for creating a success message.
	 *
	 * @method success
	 * @param {String} msg A success message 
	 * @return {Object} an AlertView instance
	 * @static
	 */
	_.each(alerts, function(alertType) {
		AlertView[alertType] = function(msg) {
			return AlertView.msg({ alert: alertType, msg: msg });
		};
	});
	
	App.views.AlertView = AlertView;
})(Catool);

/**
 * A text editor class. This is intended to be little more than a wrapper
 * around a textarea. If anything more complex than that is needed, a proper
 * text editor, wysiwyg or otherwise, should be used (i.e. tinymce, etc).
 *
 * @class CommentEditorView
 * @namespace Catool.views
 * @constructor
 */

(function(App) {
	// dependencies
	var View = App.View;
	
	var CommentEditorView = View.extend({
		events: {
			'click .js-btn-save' : 'onSave',
			'click .js-btn-cancel' : 'onCancel'
		},

		/**
		 * Class name for the container div element.
		 *
		 * @property className
		 */
		className: 'note-text-editor',

		/**
		 * Template for editor.
		 *
		 * @property template
		 */
		template: _.template([
			'<textarea style="margin-bottom: 10px; width: 95%"><%= content %></textarea>',
			'<p>',
				'<button class="js-btn-save btn-primary btn" style="margin-right: 10px">Save Changes</button>',
				'<button class="js-btn-cancel btn">Cancel</button>',
			'</p>'
		].join("")),

		/**
		 * Initialize the view.
		 *
		 * @method initialize
		 * @param {Object} options Hash of options.
		 */
		initialize: function() {
			this.minHeight = this.options.minHeight || 50;
			this.maxHeight = this.options.maxHeight || 500;
			this.save = this.options.save;  // callback
			this.cancel = this.options.cancel; //callback

			this.setHeight(this.options.height || null);
			this.setContent(this.options.content || null);
		},

		/**
		 * Sets the editor content.
		 *
		 * @method setContent
		 * @param {String} value string value 
		 * @return {Object} this
		 */
		setContent: function(value) {
			this.content = value;
			return this;
		},

		/**
		 * Sets the editor height.
		 *
		 * @method setContent
		 * @param {Number} h height
		 * @return {Object} this
		 */
		setHeight: function(h) {
			var max = this.maxHeight, min = this.minHeight;
			if(_.isNumber(max) && h > max) {
				h = max;
			} else if(_.isNumber(min) && h < min) {
				h = min;
			}

			this.height = h;

			return this;
		},

		/**
		 * Render the editor
		 *
		 * @method render
		 * @return {Object} this
		 */
		render: function() {
			var html = this.template({ content: this.content });
			var height = this.height;
			var minHeight = this.options.minHeight || 50;
			var maxHeight = this.options.maxHeight || 250; 

			this.$el.html(html);

			if(height) {
				this.$el.children('textarea').height(height);
			}

			return this;
		},

		/**
		 * Event handler for canceling an edit.
		 *
		 * @method onCancel
		 * @param {Object} evt jQuery event object
		 */
		onCancel: function(evt) {
			evt.preventDefault();
			evt.stopPropagation();
			this.cancel();
		},

		/**
		 * Event handler for saving.
		 *
		 * @method onSave
		 * @param {Object} evt jQuery event object
		 */
		onSave: function(evt) {
			evt.preventDefault();
			evt.stopPropagation();

			var value = this.$el.children('textarea').val();
			this.save(value);
		},

		/**
		 * Destroy the view.
		 *
		 * @method destroy
		 */
		 destroy: function() {
			this.remove(); // removes from dom
			this.unbind(); // removes all events/callbacks on this view
		 }
	});

	App.views.CommentEditorView = CommentEditorView;
})(Catool);

/**
 * A class for displaying and submitting the video annotation form.
 *
 * @class VideoNoteFormView
 * @namespace Catool.views
 * @constructor
 */
(function(App) {
	// dependencies
	var Note = App.models.Note;
	var View = App.View;
	var AlertView = App.views.AlertView;
	var TimeConverter = App.utils.TimeConverter;
	
	var VideoNoteFormView = View.extend({
		events: {
			'submit .note-form': 'onSubmit',
			'click button.cancel': 'onCancel'
		},

		/**
		 * Template used for rendering error message(s) at the top of the form.
		 *
		 * @property errorTemplate
		 */
		errorTemplate: _.template('<p class="help-block help-error"><%= msg %></p>'),

		/**
		 * Initialize the view.
		 *
		 * @method initialize
		 * @param {Object} options Hash of options.
		 */
		initialize: function(options) {
			var $title = this.$('input[name=title]');
			var $body = this.$('textarea[name=body]');
			var $start_time = this.$('input[name=start_time]');
			var $end_time = this.$('input[name=end_time]');
			var $tags = this.$('input[name=tags]');

			$tags.tagit(); // tag-it jquery plugin

			this.video = options.video;
			this.player = options.player;
			this.formErrors = {};
			this.inputFields = {
				Note : {
					title: $title,
					body: $body,
					tags: $tags
				},
				Segment: {
					start_time: $start_time,
					end_time: $end_time
				}
			};

			// relay time field changes to the player
			$start_time.bind('keyup', this.onTimeFieldChange(0));
			$end_time.bind('keyup', this.onTimeFieldChange(1));

			this.player.on('rangesliderchange', _.bind(this.onRangeSliderChange, this));
		},

		/**
		 * Event handler for relaying values from the player's range slider plugin
		 * to the start/end time fields in this form.
		 *
		 * @method onRangeSliderChange
		 */
		onRangeSliderChange: function() {
			var plugin = this.player.getPlugin('rangeslider');
			var status = plugin.currentStatus();
			var timeField = this.inputFields.Segment;

			var start = TimeConverter(Math.round(status.values[0])).toString();
			var end = TimeConverter(Math.round(status.values[1])).toString();

			timeField.start_time.val(start);
			timeField.end_time.val(end);
		},

		/**
		 * Event handler for relaying values from a time field to a player plugin.
		 *
		 * @method onTimeFieldChange
		 */
		onTimeFieldChange: function(index) {
			var self = this;
			return function(evt) {
				var val = TimeConverter($(this).val()).toSeconds();
				var plugin = self.player.getPlugin('rangeslider');
				if(plugin && !isNaN(val)) {
					plugin.setValue(index, val);
				}
			};
		},

		/**
		 * Event handler for submitting a single annotation.
		 *
		 * @method onSubmit
		 * @param {Object} evt jQuery event object
		 */
		onSubmit: function(evt) {
			var self = this;
			var attrs = {};
			var note = new Note();
			var errors;
			evt.preventDefault();

			this._foreachField(function(model, name, field) {
				attrs[model] = attrs[model] || {};
				attrs[model][name] = field.val();
			});
			
			attrs.Note.type = 'annotation';
			attrs.Note.target_id = this.collection.targetModel.get('id');
			attrs.Note.title = attrs.Note.title || 'Untitled';
			attrs.Segment = [attrs.Segment];
			
			this.unhighlightErrors();
			this.validateAnnotation(attrs);
			
			if(this.isValid()) {
				_.each(['start_time', 'end_time'], function(key) {
					attrs.Segment[0][key] = TimeConverter(attrs.Segment[0][key]).toSeconds();
				});
				
				note.save(attrs, {
					error: _.bind(this.handleError, this),
					success: _.bind(this.handleSave, this)
				});
			} else {
				this.message({ alert: 'error', msg: 'Annotation not saved' });
				this.highlightErrors();
			}
		},

		/**
		 * Event handler for canceling an annotation.
		 *
		 * @method onCancel
		 * @param {Object} evt jQuery event object
		 */
		onCancel: function(evt) {
			evt.preventDefault();
			this.clear();
			this.player.hideRangeSlider();
			this.trigger('cancel');
		},

		/**
		 * Validates an annotation.
		 *
		 * @method validateAnnotation
		 * @param {Object} attrs A hash of Note attributes.
		 * @return {Object|False} 
		 */
		validateAnnotation: function(attrs) {
			var errs = {};
			var body = attrs.Note.body || '';
			var segment = attrs.Segment[0];
			var start_time = segment.start_time;
			var end_time = segment.end_time;
			var duration = this.video.get('duration');
			
			// utility functions
			var isValidTime = function(t) {
				return TimeConverter(t).isValid();
			};
			var isValidSequence = function(t1, t2) {
				return TimeConverter(t1).toSeconds() <= TimeConverter(t2).toSeconds();
			};
			
			if(isValidTime(start_time) && isValidTime(end_time)) {
				if(isValidSequence(start_time, end_time)) {
					if(!isValidSequence(start_time, duration)) {
						errs.start_time = 'Start time exceeds video duration';
					}
					if(!isValidSequence(end_time, duration)) {
						errs.end_time = 'End time exceeds video duration';
					}
				} else {
					errs.start_time = 'Start time is greater than end time';
					errs.end_time = '';
				}
			} else {
				if(!isValidTime(start_time)) {
					errs.start_time = TimeConverter.invalidText;
				}
				if(!isValidTime(end_time)) {
					errs.end_time = TimeConverter.invalidText;
				}
			}

			if($.trim(body).length === 0) {
				errs.body = 'Comment is empty';
			}
			
			this.formErrors = errs;
		},

		/**
		 * Checks if the form is valid.
		 *
		 * @method isValid
		 * @return {Boolean} true if the form is valid, false otherwise.
		 */
		isValid: function() {
			return _.keys(this.formErrors).length === 0;
		},

		/**
		 * Handles a successful Note save to the server.
		 *
		 * @method handleSave
		 * @param {Object} model the Model object
		 * @param {Object} response the server response
		 */
		handleSave: function(model, response) {
			this.collection.add(model);
			this.clear();
			this.message({ alert: 'success', msg: 'Annotation saved' });
		},

		/**
		 * Handles an error saving a Note to the server. 
		 *
		 * @method handleError
		 * @param {Object} model the Model object
		 * @param {Object} response the server response
		 */
		handleError: function(model, response) {
			this.message({ alert: 'error', msg: 'Annotation not saved' });
		},

		/**
		 * Clears and resets the form.
		 *
		 * @method clear
		 */
		clear: function() {
			this.closeMessage();
			this.unhighlightErrors();
			this._foreachField(function(model, name, field) {
				if(name === 'tags') {
					field.tagit("removeAll");
				}
				field.val('');
			});		
			this.onRangeSliderChange();	
		},

		/**
		 * Displays an alert message.
		 *
		 * @method message
		 * @param {Object} options A hash of options passed to the AlertView. Should specify *type* and *msg*. See AlertView constructor.
		 */
		message: function(options) {
			var $el = this.$('.alert-messages');
			$el.html(AlertView.msg(options).el);
		},

		/**
		 * Closes an alert message.
		 *
		 * @method closeMessage
		 */
		closeMessage: function() {
			this.$('.alert-messages .alert').remove();
		},

		/**
		 * Highlight errors on the form by changing the color of 
		 * each form control with a problem and appending a message.
		 *
		 * @method highlightErrors
		 */
		highlightErrors: function() {
			var self = this;
			_.each(this.formErrors, function(value, key) {
				var $control = self.$('.control-group:has(*[name='+key+'])');
				$control.addClass('error');
				$control.find('.controls').filter(':first').append(self.errorTemplate({msg: value }));
			});
		},

		/**
		 * Unhighlight erros on the form.
		 *
		 * @method unhighlightErrors
		 */
		unhighlightErrors: function() {
			this.$('.control-group.error').removeClass('error');
			this.$('.help-error').remove();
		},

		/**
		 * Helper function to execute a callback for each field 
		 * that is displayed on the form.
		 *
		 *
		 * @private
		 * @method _foreachField
		 * @param {Function} callback A callback function
		 */
		_foreachField: function(callback) {
			var self = this;
			var attrs = {};
			_.each(_(this.inputFields).keys(), function(modelKey) {
				var modelFields = self.inputFields[modelKey];
				_.each(modelFields, function (formField, fieldName) {
					callback.call(this, modelKey, fieldName, formField);
				});
			});			
		}
	});

	App.views.VideoNoteFormView = VideoNoteFormView;
})(Catool);

/**
 * A class for displaying and configuring the player.
 *
 * See VideoJS docs: http://videojs.com/docs/api/
 * 
 * @class VideoPlayerView
 * @namespace Catool.views
 * @constructor
 */
(function(App, _V_) {
	// dependencies
	var View = App.View;
	
	var VideoPlayerView = View.extend({
		
		/**
		 * Reference to player object.
		 * 
		 * @property player
		 */
		player: null,

		/**
		 * List of events relayed from the player to this view.
		 * 
		 * @property player
		 */		
		playerEvents: [
			'loadstart', 
			'loadedmetadata', 
			'loadeddata', 
			'loadedalldata',
			'play',
			'pause',
			'ended',
			'durationchange',
			'progress',
			'resize',
			'volumechange',
			'error',
			'fullscreenchange',
			'rangesliderchange', // slider plugin handle changed
			'rangeslidererror', // slider plugin error
			'rangesliderlock', // slider plugin locked
			'rangesliderunlock' // slider plugin unlocked
		],

		/**
		 * Initialize the view.
		 *
		 * @method initialize
		 * @param {Object} options Hash of options.
		 */
		initialize: function(options) {
			this.playerId = options.playerId;
		},

		/**
		 * Render the player.
		 *
		 * @method render
		 * @return {Object} this
		 */
		render: function() {
			var self = this;
			var addOptions = { 
				techOrder: ['html5', 'youtube', 'flash'],
				plugins: ['rangeslider']
			};

			// stores a reference to the player and then calls onPlayerReady()
			_V_(this.playerId, addOptions, function() {
				self.player = this;
				self.onPlayerReady.apply(self, arguments);
			});
			
			return this;
		},

		/**
		 * Callback to initialize player when it's ready.
		 *
		 * @method onPlayerReady
		 */
		onPlayerReady: function() {
			this._relayPlayerEvents();
			this.trigger('ready', this);
		},
		
		/**
		 * Helper function to relay player events to the view.
		 * 
		 * @method _relayPlayerEvents
		 */
		_relayPlayerEvents: function() {
			var self = this;
			var makeTrigger = function(eventName) {
				return function() {
					var args = Array.prototype.slice.call(arguments);
					args.splice(0, 0, eventName);
					self.trigger.apply(self, args);
				};
			};
					
			_.each(this.playerEvents, function(eventName) {
				self.player.addEvent(eventName, makeTrigger(eventName));
			});
		},

		/**
		 * Handler for playing a media segment.
		 *
		 * @method playSegment
		 * @param {Object} a Note model
		 * @param {Object} a View displaying the Note
		 */
		playSegment: function(start, end) {
			if(start !== null && end !== null) {
				this.initSegmentHandler(start, end);
				this.player.currentTime(start);
				this.player.play();

				this.showRangeSlider(); 
				this.setRangeSliderValues(start, end);
				this.lockRangeSlider();
			}
		},

		/**
		 * Initializes a callback function to handle "timeupdate" events in player.
		 *
		 * @method initSegmentHandler
		 * @param {Number} start time in seconds
		 * @param {Number} end time in seconds
		 */
		initSegmentHandler: function(start, end) {
			this.clearSegmentHandler();
			this.segmentHandler = this.makeSegmentHandler(start, end);
			this.player.addEvent('timeupdate', this.segmentHandler);
		},

		/**
		 * Clears the segment handler.
		 *
		 * @method clearSegmentHandler
		 */
		clearSegmentHandler: function() {
			if(this.segmentHandler) {
				this.player.removeEvent('timeupdate', this.segmentHandler);
				this.hideRangeSlider();
				this.unlockRangeSlider();
				this.segmentHandler = null;
			}
		},

		/**
		 * Creates a callback that will stop playback when the end of the segment is reached.
		 *
		 * @method makeSegmentHandler
		 * @param {Number} start time in seconds
		 * @param {Number} end time in seconds
		 */
		makeSegmentHandler: function(start, end) {
			var lower_bound = Math.floor(start);
			var upper_bound = Math.ceil(end) + 1; // extra padding for pause window
			var last_time = null;

			// only execute callback once we know the current time has changed
			var doAfterTimeChanges = _.bind(function(callback) {
				return _.bind(function(e) {
					var now = this.player.currentTime();
					if(last_time === null || last_time === now) {
						last_time = now;
					} else {
						callback.apply(this, arguments);
					}
				}, this);
			}, this);

			var callback = function(e) {
				var now = this.player.currentTime();
				var is_pause_window = (now >= end && now <= upper_bound);

				//console.log(e, 'start:', start, 'end:', end, 'now:', now);

				if(now == end || is_pause_window) {
					this.clearSegmentHandler();
					this.player.pause();
				} else if(now < lower_bound || now > upper_bound) {
					this.clearSegmentHandler();	
				}

			};

			return doAfterTimeChanges(callback);
		},

		/**
		 * Creates cue points that will trigger "cue" events.
		 *
		 * @method cue
		 * @param {Array} points an array of time values (in seconds)
		 * @param {Object} options a set of options
		 * @return undefined
		 */
		cue: function(points, options) {
			this.cues = this.cues || [];
			if(options.reset) {
				this.cues = [];
			}

			this.cues = _.uniq(this.cues.concat(points)).sort(function(a, b) {
				return a - b;
			});
			
			if(this.cues.length > 0) {
				this.initCueHandler();
			} else {
				this.clearCueHandler();
			}
		},

		/**
		 * Initializes a callback function to handle "timeupdate" events.
		 *
		 * @method initCueHandler
		 * @return undefined
		 */
		initCueHandler: function() {
			this.clearCueHandler();
			this.cueHandler = _.bind(this.onTimeCheckCuePoints, this); 
			this.player.addEvent('timeupdate', this.cueHandler);
		},

		/**
		 * Clears the cue handler.
		 *
		 * @method clearCueHandler
		 * @return undefined
		 */
		clearCueHandler: function() {
			if(this.cueHandler) {
				this.player.removeEvent('timeupdate', this.cueHandler);
				this.cueHandler = null;
			}
			this.cueIdx = -1;
			this.cueTime = -1;
			this.cueInit = false;
		},
		
		/**
		 * Handler for "timeupdate" events that will check cue points
		 * and trigger a cue event if a cue point is reached.
		 *
		 * @method onTimeCheckCuePoints
		 * @param {Object} e an event object
		 * @return undefined
		 */
		onTimeCheckCuePoints: function(e) {
			var now = this.player.currentTime();
			var cueIdx = this.cueIdx;
			var cueTime = this.cueTime;
			var nextTime;

			if(!this.cueInit) {
				this.triggerCueReady(now);
				this.cueInit = true;
			}

			if(cueIdx >= 0 && now < cueTime) {
				this.triggerCue(now);
			} else {
				nextTime = this.cues[cueIdx + 1];
				if(typeof nextTime !== 'undefined' && now >= nextTime) {
					this.triggerCue(now);
				}
			}
		},

		/**
		 * Triggers a cue event on the player.
		 *
		 * @method triggerCue
		 * @param {Number} playerTime the time at which it is triggered
		 * @return undefined
		 */
		triggerCue: function(currentTime) {
			this.updateCuePoint(currentTime);

			// NOTE: cueTime <= currentTime (they may differ slightly)
			this.trigger('cue', this, currentTime, this.cueTime);
		},

		/**
		 * Triggers a cue "ready" event on the player. This is fired
		 * once the player is ready to check cue points. 
		 *
		 * @method triggerCue
		 * @param {Number} playerTime the time at which it is triggered
		 * @return undefined
		 */
		triggerCueReady: function(currentTime) {
			this.trigger('cueready', this, currentTime, Math.floor(currentTime));
		},

		/**
		 * Updates the player's state of the next/previous cue points.
		 *
		 * @method updateCuePoint
		 * @param {Number} time in seconds
		 * @return undefined
		 */
		updateCuePoint: function(time) {
			var cueIdx = -1;
			var nextCueIdx = this.findCueIndex(function(point) {
				return point > time;
			});

			if(nextCueIdx < 0 && time >= _(this.cues).last()) {
				cueIdx = this.cues.length - 1;
			} else if(nextCueIdx >= 0){
				cueIdx = nextCueIdx - 1;
			}

			this.cueIdx = cueIdx;
			this.cueTime = cueIdx >= 0 ? this.cues[cueIdx] : -1;
			this.nextCueTime = nextCueIdx >= 0 ? this.cues[nextCueIdx] : -1;
		},
		
		/**
		 * Finds the first index of a cue according to a truth test
		 * (i.e. callback) applied to each cue.
		 *
		 * @method findCueIndex
		 * @param {Function} callback a truth test to apply to each cue
		 * @return {Number} index of cue or -1 if not found.
		 */
		findCueIndex: function(callback) {
			var i, len = this.cues.length;
			var cues = this.cues;
			
			for(i = 0; i < len; i++) {
				if(callback(cues[i])) {
					return i;
				}
			}
			
			return -1;
		},

		/**
		 * Get a reference to a player plugin.
		 *
		 * @method getPlugin
		 * @return object
		 */
		getPlugin: function(name) {
			return this.player.getPlugin(name);
		},

		/**
		 * Gets the range slider plugin and executes a callback.
		 *
		 * @method rangeSliderDo
		 */
		rangeSliderDo: function(callback) {
			var plugin;
			if(this.player) {
				plugin = this.player.getPlugin('rangeslider');
				if(plugin) {
					if(typeof callback === 'function') {
						return callback.call(this, plugin);
					} else if(typeof callback === 'string') {
						return plugin[callback].call(plugin);
					}
				}
			}
			return false;
		},

		/**
		 * Display the range slider UI on the video player.
		 * For annotating IN/OUT points.
		 *
		 * @method showRangeSlider
		 */
		showRangeSlider: function(locked) {
			this.rangeSliderDo('show');
		},

		/**
		 * Hides the range slider. 
		 *
		 * @method hideRangeSlider
		 */
		hideRangeSlider: function() {
			this.rangeSliderDo('hide');
		},

		/**
		 * Lock range slider.
		 *
		 * @method lockRangeSlider
		 */
		lockRangeSlider: function() {
			this.rangeSliderDo('lock');
		},

		/**
		 * Unlock range slider.
		 *
		 * @method unlockRangeSlider
		 */
		unlockRangeSlider: function() {
			this.rangeSliderDo('unlock');
		},

		/**
		 * Sync the range slider to the current time.
		 *
		 * @method syncRangeSlider
		 */
		syncRangeSlider: function() {
			this.rangeSliderDo(function(plugin) {
				plugin.setValue(1, this.player.duration());
				plugin.setValue(0, this.player.currentTime());
			});
		},

		/**
		 * Reset the range slider to span the width of the seek bar. 
		 *
		 * @method resetRangeSlider
		 */
		resetRangeSlider: function() {
			this.rangeSliderDo(function(plugin) {
				plugin.setValue(1, this.player.duration());
				plugin.setValue(0, 0);
			});
		},

		/**
		 * Set the range slider handle values.
		 *
		 * @method setRangeSliderValues
		 */
		setRangeSliderValues: function(left, right) {
			this.rangeSliderDo(function(plugin) {
				plugin.setValue(1, right, true);
				plugin.setValue(0, left, true);
			});
		}
	});
	
	App.views.VideoPlayerView = VideoPlayerView;
})(Catool, _V_);

/**
 * A class for viewing a single comment.
 *
 * @class CommentView
 * @namespace Catool.views
 * @constructor
 */
(function(App) {
	// dependencies
	var View = App.View;
	var nl2br = App.utils.nl2br;
	var AlertView = App.views.AlertView;
	var CommentEditorView = App.views.CommentEditorView;
	
	var CommentView = View.extend({
		events: {
			'click .js-btn-edit' : 'onEdit',
			'click .js-btn-delete' : 'onDelete'
		},
		
		/**
		 * Tag name.
		 *
		 * @property tagName
		 */
		 tagName: 'li',
		 
		/**
		 * Class name for the container div element.
		 *
		 * @property className
		 */
		className: 'note note-comment',

		/**
		 * Template for displaying the comment.
		 *
		 * @property template
		 */
		template: _.template([
			'<div class="note-body">',
				'<div class="note-byline">On <%= created_date %> <%= author %>:</div>',
				'<div class="note-text"><%= body %></div>',
				'<ul class="note-actions">',
					'<li><a class="js-btn-edit <%= actionCls %>" href="javascript:;">Edit</a><li>',
					'<li><a class="js-btn-delete <%= actionCls %>" href="javascript:;">Delete</a><li>',
				'</ul>',
			'</div>'
		].join("")),

		/**
		 * Initialize the view.
		 *
		 * @method initialize
		 * @param {Object} options Hash of options.
		 */
		initialize: function() {
			this.model.bind('change', this.render, this);
			this.model.bind('remove destroy', this.destroy, this);
		},

		/**
		 * Render the alert to the dom and wire up the close button.
		 *
		 * @method render
		 * @return {Object} this
		 */
		render: function() {
			var data = this.model.toJSON();

			data.body = nl2br(data.body);
			data.author = this.model.get('user').get('fullname');
			data.created_date = this.renderDate(data.created_unix);
			data.actionCls = (App.user.canModerate(this.model.get('user').get('id')) ? '' : 'hide');

			this.$el.html(this.template(data));
			this.$el.attr('id', 'note-' + this.model.id);
			this.$el.addClass('note-'+this.model.id);
			if(this.model.get('highlightAdmin')) {
				this.$el.addClass('role-admin');
			}
			
			return this;
		},

		/**
		 * Event handler for editing a comment.
		 *
		 * @method onEdit
		 * @param {Object} evt jQuery event object
		 */
		onEdit: function(evt) {
			evt.preventDefault();
			evt.stopPropagation();

			var bodyEl = this.getBodyEl();
			var actionsEl = this.getActionsEl();

			var editor = new CommentEditorView({
				height: bodyEl.height(),
				content: this.model.get('body'),
				save: _.bind(this.onSaveEdit, this),
				cancel: _.bind(this.onCancelEdit, this)
			});

			bodyEl.html(editor.render().el);
			actionsEl.hide();

			if(this.editor) {
				this.editor.destroy();
			}
			this.editor = editor;
		},

		/**
		 * Event handler for saving an edit.
		 *
		 * @method onSaveEdit
		 * @param {Object} view texteditor view
		 * @param {String} value content of editor
		 */
		onSaveEdit: function(value) {
			this.model.save({ body: value }, { 
				wait: true,
				success: _.bind(this.render, this),
				error: _.bind(this._onSaveError, this)
			});
		},

		/**
		 * Event handler for canceling an edit.
		 *
		 * @method onCancelEdit
		 */
		onCancelEdit: function() {
			var body = nl2br(this.model.get('body'));
			this.getBodyEl().html(body);
			this.getActionsEl().show();
		},

		/**
		 * Event handler for deleting a comment.
		 *
		 * @method onDelete
		 * @param {Object} evt jQuery event object
		 */
		onDelete: function(evt) {
			evt.preventDefault();
			evt.stopPropagation();

			if(window.confirm("OK to delete comment?")) {
				this.model.destroy({ 
					wait: true,
					error: _.bind(this._onDeleteError, this)
				});
			}
		},

		/**
		 * Returns the element containing note body.
		 *
		 * @method getBodyEl
		 * @return {Object} jquery element
		 */
		getBodyEl: function() {
			return this.$('.note-text').first();
		},

		/**
		 * Returns the element containing note actions.
		 *
		 * @method getActionsEl
		 * @return {Object} jquery element
		 */
		getActionsEl: function() {
			return this.$('.note-actions').first();
		},

		/**
		 * Destroy the view.
		 *
		 * @method destroy
		 */
		 destroy: function() {
			this.remove(); // removes from dom
			this.unbind(); // removes all events/callbacks on this view
			this.model.unbind('remove destroy', this.destroy, this);
			this.model.unbind('change', this.render);

			if(this.editor) {
				this.editor.destroy();
			}
		 },

		/**
		 * Renders a unix time as a date string.
		 *
		 * @method renderDate
		 * @param {Number} date_unix unix timestamp
		 * @return {String} date string
		 */
		renderDate: function(date_unix) {
			if(typeof date_unix === 'number') {
				return moment.unix(date_unix).format('MMMM Do YYYY, h:mm a');
			}
			return 'n/a';
		},


		_onDeleteError: function(originalModel, resp, options) {
			var msg = resp.statusText ? ': '+resp.statusText : '';
			this._showMsg('Error deleting comment' + msg);
		},

		_onSaveError: function(originalModel, resp, options) {
			var msg = resp.statusText ? ': '+resp.statusText : '';
			this._showMsg('Error saving comment' + msg);
		},

		_showMsg: function(msg, statusText) {
			this.$el.children('.action-messages').remove();
			this.$el.append('<div class="action-messages"></div>');
			this.$el.children('.action-messages').append(AlertView.error(msg).el);
		}

	});

	App.views.CommentView = CommentView;
})(Catool);

/**
 * A class for creating a comment form.
 *
 * @class CommentFormView
 * @namespace Catool.views
 * @constructor
 */
(function(App) {
	// dependencies
	var View = App.View;
	var Note = App.models.Note;
	
	var CommentFormView = View.extend({
		events: {
			'click .js-btn-cancel' 		: 'onCancelComment',
			'submit'					: 'onSaveComment'
		},

		/**
		 * Class name for the container div element.
		 *
		 * @property className
		 */
		className: 'hide',

		/**
		 * Template for displaying the annotation.
		 *
		 * @property template
		 */
		template: _.template([
			'<form class="note-comment-form">',
				'<textarea class="note-comment-text" rows="3"></textarea>',
				'<div>',
					'<button type="submit" class="js-btn-save btn btn-primary">Submit Comment</button>',
					'<button class="js-btn-cancel btn">Cancel</button>',
				'</div>',
			'</form>'
		].join("")),

		/**
		 * Initialize the comment form.
		 *
		 * @method initialize
		 * @param {Object} options Hash of options.
		 */
		initialize: function() {
		},

		/**
		 * Renders the form.
		 *
		 * @method render
		 * @return {Object} this
		 */
		render: function() {
			this.$el.html(this.template());
			return this;
		},

		/**
		 * Event handler for saving a comment.
		 *
		 * Note: the comment is only added to the collection *after* the server has accepted it.
		 *
		 * @method onSaveComment
		 * @param {Object} evt jQuery event object
		 */
		onSaveComment: function(evt) {
			evt.preventDefault();
			evt.stopPropagation();

			var self = this;
			var text = this.$('.note-comment-text').val() || '';
			if($.trim(text).length === 0) {
				return;
			}

			var note = new Note();
			var attrs = {
				target_id: this.model.get('target_id'),
				parent_id: this.model.get('id'),
				type: 'comment',
				body: text
			};

			note.save({ Note: attrs }, {
				error: function(model, response) {
					//console.log('error', model, response, self);
				},
				success: function(model, response) {
					self.model.collection.add(model);
					self.reset();
					self.toggle();
				}
			});
		},

		/**
		 * Toggles display of the comment.
		 *
		 * @method toggle
		 */
		toggle: function() {
			this.$el.toggleClass('hide');
		},

		/**
		 * Clears the comment form.
		 *
		 * @method reset
		 */
		reset: function() {
			this.$('.note-comment-text').val('');
		},

		/**
		 * Event handler for canceling a comment.
		 *
		 * @method onCancelComment
		 * @param {Object} evt jQuery event object
		 */
		onCancelComment: function(evt) {
			evt.preventDefault();
			evt.stopPropagation();

			this.reset();
			this.toggle();
		},

		/**
		 * Destroy the view.
		 *
		 * @method destroy
		 */
		 destroy: function() {
			this.remove(); // removes from dom
			this.unbind(); // removes all events/callbacks on this view
		 }
	});

	App.views.CommentFormView = CommentFormView;
})(Catool);

/**
 * A class for displaying a single video annotation.
 *
 * @class VideoAnnotationView
 * @namespace Catool.views
 * @constructor
 */
(function(App) {
	// dependencies
	var View = App.View;
	var Note = App.models.Note;
	var CommentView = App.views.CommentView;
	var CommentFormView = App.views.CommentFormView;
	var AlertView = App.views.AlertView;
	var CommentEditorView = App.views.CommentEditorView;
	var TimeConverter = App.utils.TimeConverter;
	var nl2br = App.utils.nl2br;
	
	var VideoAnnotationView = View.extend({

		events: {
			'click .js-note-header': 'onToggleDetails',
			'click .js-note-control-play': 'onPlay',
			'click .js-btn-reply' : 'onToggleComment',
			'click .js-btn-comments' : 'onToggleComments',
			'click .js-btn-edit' : 'onEdit',
			'click .js-btn-delete' : 'onDelete'
		},
		
		/**
		 * Tag name.
		 *
		 * @property tagName
		 */
		 tagName: 'li',
		 	
		/**
		 * Class name for the container div element.
		 *
		 * @property className
		 */
		className: 'note note-annotation',

		/**
		 * Template for displaying the annotation.
		 *
		 * @property template
		 */
		template: _.template([
			'<div class="note-header js-note-header">',
				'<div class="note-col note-title"><%= title %></div>',
				'<div class="note-col">',
					'<div class="note-date"><%= created_date %></div>',
					'<div class="note-author"><%= author %></div>',
				'</div>',
				'<div class="note-clear-float"></div>',
			'</div>',
			'<div class="note-body <%= expandedCls %>">',
				'<div class="note-control note-control-play js-note-control-play">',
					'<span class="note-play-label">Play</span>',
					'<span class="note-play-time"><%= start_time %> / <%= end_time %></span>',
				'</div>',
				'<div class="note-byline">On <%= created_date %> <%= author %>:</div>',
				'<div class="note-text"><%= body %></div>',
				'<div class="note-footer">',
					'<%= tags %>',
					'<ul class="note-actions">',
						'<li><a class="js-btn-reply <%= replyCls %>" href="#">Comment</a></li>',
						'<li><a class="js-btn-comments hide" href="#">Show Comments (<span class="note-num-comments"><%= numComments %></span>)</a></li>',
						'<li><a class="js-btn-edit <%= actionCls %>" href="#">Edit</a></li>',
						'<li><a class="js-btn-delete <%= actionCls %>" href="#">Delete</a></li>',
					'</ul>',
					'<ul class="notes note-comments"></ul>',
				'</div>',
			'</div>'
		].join("")),

		/**
		 * Template for displaying annotation tags.
		 *
		 * @property tagsTemplate
		 */
		tagsTemplate: _.template([
			'<ul class="note-tags">',
				'<li><b>Tags:</b></li>',
				'<% _.each(tags, function(tag) { %>',
					'<li><%= tag %></li>',
				'<% }); %>',
			'</ul>'
		].join("")),

		/**
		 * Template for editing a comment.
		 *
		 * @property editTemplate
		 */
		editTemplate: _.template([
			'<form class="note-annotation-edit-form">',
				'<textarea style="width: 95%"><%= body %></textarea>',
				'<p>',
					'<button class="save-edits btn-primary btn" style="margin-right: 10px">Save Changes</button>',
					'<button class="cancel-edits btn">Cancel</button>',
				'</p>',
			'</form>'
		].join("")),

		/**
		 * Initialize the annotation view.
		 *
		 * @method initialize
		 * @param {Object} options Hash of options.
		 */
		initialize: function() {
			this.views = [];
			this.commentForm = null;
			this.numComments = 0;
			this.parentView = this.options.parentView;
			this.lockComments = this.options.lockComments;
			this.state = {
				showComments: false,
				showDetails: false
			};
			_.extend(this.state, this.options.state);

			this.model.on('remove destroy', this.destroy, this);
			this.on('comment:destroy', this.onDestroyComment, this);
			this.on('comment:add', this.onAddComment, this);
		},

		/**
		 * Renders the annotation and its comments (if any).
		 *
		 * @method render
		 * @return {Object} this
		 */
		render: function() {
			var data = this.getDataForTemplate();
			var commentForm = new CommentFormView({ model: this.model });

			this.$el.attr('id', 'note-' + this.model.id);		
			this.$el.html(this.template(data));
			if(this.model.get('highlightAdmin')) {
				this.$el.addClass('role-admin');
			}
			this.$('.note-comments').before(commentForm.render().el);
			this.renderComments();
			this.commentForm = commentForm;

			return this;
		},

		/**
		 * Renders comments.
		 *
		 * @method renderComments
		 * @return {Object} this
		 */
		renderComments: function() {
			var el, cls;
			var views = [];
			var comments = this.getComments();

			if(comments.length > 0) {
				cls = 'notes note-comments'; 
				el = $(this.make('div', { 
						'class': cls + (this.state.showComments ? '' : ' hide')
				}));
				 
				_.each(comments, function(comment) {
					var view = new CommentView({ model: comment });
					views.push(view);
					el.append(view.render().el);
				});

				this.$('.note-comments').replaceWith(el);
				this.$('.js-btn-comments').removeClass('hide');

				_.each(this.views, function(view) {
					view.destroy();
				});

				this.views = views;
			} else {
				this.$('.note-comments').addClass('hide');
			}
			
			return this;
		},

		/**
		 * Prepares the data for the comment template.
		 *
		 * @method getDataForTemplate
		 * @return {Object} template variables
		 */
		getDataForTemplate: function() {
			var data = this.model.toJSON();
			var segments = this.model.get('segments');
			var segment = segments.first();
			var user_id = this.model.get('user').get('id');
			
			data.body = nl2br(data.body);
			data.author = this.model.get('user').get('fullname');
			data.created_date = 'n/a';

			data.tags = '';
			if(this.model.hasTags()) {
				data.tags = this.tagsTemplate({ 
					tags: this.model.get('tags').pluck('name')
				});
			} 

			if(data.created_unix) {
				data.created_date = moment.unix(data.created_unix).format('MMMM Do YYYY, h:mm a');
			}

			_(['start_time', 'end_time']).each(function(key) {
				if(segment) {
					var t = segment.get(key);
					data[key] = TimeConverter(t).toString();
				}
			});

			data.numComments = this.getNumComments();
			data.expandedCls = (this.state.showDetails ? '' : 'hide');
			data.actionCls = (App.user.canModerate(user_id) ? '' : 'hide');
			data.replyCls = this.lockComments ? 'hide' : '';
			
			return data;
		},

		/**
		 * Retrieves all of the comments for this annotation.
		 *
		 * @method getComments
		 * @return {Array} a list of Note models 
		 */
		getComments: function() {
			var comments = this.model.getComments();
			comments.sort(function(left, right) {
				var l = left.get('created_unix');
				var r = right.get('created_unix');
				return l - r;
			});

			return comments;
		},

		/**
		 * Retrieves the total number of comments for this annotation.
		 *
		 * @method getNumComments
		 * @return {Array} a list of Note models 
		 */
		getNumComments: function() {
			var comments = this.getComments();
			this.numComments = comments.length;
			return this.numComments;
		},

		/**
		 * Toggle display of the comment comment form. 
		 *
		 * @method toggleComment
		 */
		toggleComment: function() {
			this.commentForm.toggle();
		},

		/**
		 * Toggle display of the annotation details.
		 *
		 * @method toggleDetails
		 * @param state {Boolean} state A true value forces the details to be displayed, false to be hidden.
		 * @param {Boolean} suppressEvent stops the toggle event from being fired
		 */
		toggleDetails: function(state, suppressEvent) {
			var toggleState = _.isBoolean(state) ? state : this.state.showDetails;
			this.$('.note-body').toggleClass('hide', toggleState);
			if(!suppressEvent) {
				this.trigger('toggle', this, this.model.id, toggleState);
			}
			this.state.showDetails = !toggleState;
		},
		
		/**
		 * Collapse the annotation.
		 *
		 * @method collapse
		 */
		collapse: function() {
			this.toggleDetails(true, true);
			return this;
		},
		
		/**
		 * Expand the annotation.
		 *
		 * @method collapse
		 */
		expand: function() {
			this.toggleDetails(false, true);
			return this;
		},
		
		/**
		 * Collapse the comments.
		 *
		 * @method collapse
		 */
		collapseComments: function() {
			this.toggleComments(true, true);
			return this;
		},
		
		/**
		 * Expand the comments.
		 *
		 * @method collapse
		 */
		expandComments: function() {
			if(this.getNumComments() > 0) {
				this.toggleComments(false, true);
			}
			return this;
		},
		
		/**
		 * Show the annotation.
		 *
		 * @method collapse
		 */
		show: function() {
			this.$el.show();
			return this;
		},

		/**
		 * Show the annotation.
		 *
		 * @method collapse
		 */
		hide: function() {
			this.$el.hide();
			return this;
		},
		
		/**
		 * Toggle display of the annotation comments.
		 *
		 * @method toggleComments
		 * @param state {Boolean} state A true value forces the replies to be displayed, false to be hidden.
		 * @param {Boolean} suppressEvent stops the toggle event from being fired
		 */
		toggleComments: function(state, suppressEvent) {
			var toggleState = _.isBoolean(state) ? state : this.state.showComments;
			this.$('.note-comments').toggleClass('hide', toggleState);
			if(!suppressEvent) {
				this.trigger('toggle', this, this.model.id, toggleState);
			}
			this.state.showComments = !toggleState;
		},

		/**
		 * Event handler for toggling annotaiton details.
		 *
		 * @method onToggleDetails
		 * @param {Object} evt jQuery event object
		 */
		onToggleDetails: function(evt) {
			evt.preventDefault();
			this.toggleDetails();
		},

		/**
		 * Event handler for toggling comment form.
		 *
		 * @method onToggleComment
		 * @param {Object} evt jQuery event object
		 */
		onToggleComment: function(evt) {
			evt.preventDefault();
			this.toggleComment();
		},

		/**
		 * Event handler for toggling the list of replies.
		 *
		 * @method onToggleComments
		 * @param {Object} evt jQuery event object
		 */
		onToggleComments: function(evt) {
			evt.preventDefault();
			this.toggleComments();
		},

		/**
		 * Event handler for playing the annotation.
		 *
		 * @method onPlay
		 * @param {Object} evt jQuery event object
		 */
		onPlay: function(evt) {
			evt.preventDefault();
			this.parentView.trigger('playNote', this.model, this);
		},

		/**
		 * Event handler for destroying a comment.
		 *
		 * @method onDestroyComment
		 * @param {Object} evt jQuery event object
		 */
		onDestroyComment: function(model, collection, options) {
			this.updateNumComments(-1);
		},

		/**
		 * Event handler for adding a comment.
		 *
		 * @method onAddComment
		 * @param {Object} evt jQuery event object
		 */
		onAddComment: function(model, collection, options) {
			this.updateNumComments(1);
			this.renderComments();
		},

		/**
		 * Event handler for editing an annotation.
		 *
		 * @method onEdit
		 * @param {Object} evt jQuery event object
		 */
		onEdit: function(evt) {
			evt.preventDefault();
			evt.stopPropagation();

			var bodyEl = this.getBodyEl();
			var actionsEl = this.getActionsEl();

			var editor = new CommentEditorView({
				height: bodyEl.height(),
				content: this.model.get('body'),
				save: _.bind(this.onSaveEdit, this),
				cancel: _.bind(this.onCancelEdit, this)
			});

			bodyEl.html(editor.render().el);
			actionsEl.hide();

			if(this.editor) {
				this.editor.destroy();
			}
			this.editor = editor;
		},

		/**
		 * Event handler for saving an edit.
		 *
		 * @method onSaveEdit
		 * @param {Object} view texteditor view
		 * @param {String} value content of editor
		 */
		onSaveEdit: function(value) {
			var bodyEl = this.getBodyEl();

			this.model.save({ body: value }, { 
				wait: true,
				success: _.bind(this.render, this),
				error: function() {
					bodyEl.append(AlertView.error('Error saving changes').el); 
				}
			});
		},

		/**
		 * Event handler for canceling an edit.
		 *
		 * @method onCancelEdit
		 */
		onCancelEdit: function() {
			var body = nl2br(this.model.get('body'));
			this.getBodyEl().html(body);
			this.getActionsEl().show();
		},

		/**
		 * Event handler for deleting an annotation.
		 *
		 * @method onDelete
		 * @param {Object} evt jQuery event object
		 */
		onDelete: function(evt) {
			evt.preventDefault();
			if(window.confirm("OK to delete annotation?")) {
				this.model.destroy({ wait: true });
			}
		},

		/**
		 * Updates the number of comments on the view.
		 *
		 * @method updateNumComments
		 * @param {Number} increment a positive or negative integer
		 */
		updateNumComments: function(increment) {
			this.numComments += increment;
			this.$('.note-num-comments').html(this.numComments);
			if(this.numComments < 1) {
				this.$('.js-btn-comments').addClass('hide');
			}
		},

		/**
		 * Returns the element containing note body.
		 *
		 * @method getBodyEl
		 * @return {Object} jquery element
		 */
		getBodyEl: function() {
			return this.$('.note-text').first();
		},

		/**
		 * Returns the element containing note actions.
		 *
		 * @method getActionsEl
		 * @return {Object} jquery element
		 */
		getActionsEl: function() {
			return this.$('.note-actions').first();
		},

		/**
		 * Destroy the view. This should handle all cleanup that needs to
		 * happen when a view is being destroyed or closed. This includes the
		 * following tasks:
		 * 
		 * 		- Remove the view from the dom
		 * 		- Remove backbone events/callbacks on the view itself
		 * 		- Remove backbone events/callbacks registered on other objects
		 *
		 * @method destroy
		 */
		 destroy: function() {
			this.parentView = null;
			if(this.commentForm) {
				this.commentForm.destroy();
			}
			if(this.editor) {
				this.editor.destroy();
			}
			this.remove(); // removes from dom
			this.unbind(); // removes all events/callbacks on this view
			this.model.unbind(); // removes all callbacks on the model
		 }
	});
	
	App.views.VideoAnnotationView = VideoAnnotationView;
})(Catool);

/**
e* A class for displaying a list of annotations and their
 * associated comments.
 *
 * @class VideoNotesView
 * @namespace Catool.views
 * @constructor
 */
(function(App) {
	// dependencies
	var View = App.View;
	var VideoAnnotationView = App.views.VideoAnnotationView;
	var TimeConverter = App.utils.TimeConverter;

	var VideoNotesView = View.extend({
		events: {
			'click .notes-refresh' : 'onRefresh',
			'click .notes-toggle' : 'onToggleDetails',
			'click .notes-sync' : 'onToggleSync',
			'submit .form-search' : 'onSearch',
			'click .notes-sort a' : 'onSort'
		},

		/**
		 * Text displayed if the note collection is empty.
		 *
		 * @property emptyText
		 * @type String
		 */
		emptyText: 'No annotations to display.',

		/**
		 * Template for displaying the empty text.
		 *
		 * @property emptyTemplate
		 * @type String
		 */
		emptyTemplate: _.template('<div class="notes notes-empty"><%= msg %></div>'),

		/**
		 * Display the notes collapsed.
		 *
		 * @property collapsed
		 * @type Boolean
		 */
		collapsed: true,

		/**
		 * Initialize the view.
		 *
		 * @method initialize
		 * @param {Object} options Hash of options.
		 */
		initialize: function(options) {
			this.views = [];
			this._viewStates = {};

			this.searchInputEl = this.$('input.search-query');
			this.collapseBtnEl = this.$('.notes-toggle');
			this.syncBtnEl = this.$('.notes-sync');

			this.syncModel = options.syncModel;
			this.syncEnabled = options.syncEnabled;
			this.highlightAdmins = options.highlightAdmins;
			this.lockAnnotations = options.lockAnnotations;
			this.lockComments = options.lockComments;
			
			if(options.sortConfig) {
				this.sortBy(options.sortConfig.key, options.sortConfig.dir);
			} else {
				this.sortBy('start-time', 'asc');
			}

			this.initCollection();
			this.updateSyncBtn();
			this.syncModel.initialize(this);
		},
		
		/**
		 * Initialize the view collection events and other filters.
		 *
		 * @return void
		 */
		initCollection: function() {

			this.initHighlightAdmins();
			this.collection.on('reset', this.render, this);
			this.collection.on('add', this.onAddAnnotation, this);
			this.collection.on('add', this._relayToCommentView('comment:add'), this);
			this.collection.on('destroy', this._relayToCommentView('comment:destroy'), this);
		 },

		/**
		 * Initialize the behavior for highlighting admin notes.
		 *
		 * @return void
		 */
		initHighlightAdmins: function() {
		 	var check = {}; // map of admin user IDs

			var setHighlight = function(model) {
				 var user = model.get('user');
				 var user_id = user.get('id');
				 if(check[user_id]) {
					 model.set({ highlightAdmin: true }, {silent:true});
				 }
			};
				
			if(this.highlightAdmins && this.highlightAdmins.length > 0) {
				_.each(this.highlightAdmins, function(id) {
						 check[id] = true;
				});
				
				this.collection.on('change add sync', setHighlight);
				this.collection.on('reset', function() {
					this.each(setHighlight);
				});
				this.collection.each(setHighlight);
			}
		},

		/**
		 * This generates an event handler for collections. It is intended
		 * to be used for relaying comment-related events to the appropriate
		 * sub-view. 
		 *
		 * @method _delegateForComments
		 * @param {String} eventName the event to trigger
		 * @return {Function} a callback function which 
		 */
		_relayToCommentView: function(eventName) {
			return function(model, collection, options) {
				_(this.views).each(function(view) {
					if(model.isCommentOn(view.model)) {
						view.trigger(eventName, model, collection, options);
					}
				});
			};
		},

		/**
		 * Event handler for sorting notes.
		 *
		 * @method onSort
		 * @param {Object} evt jQuery event object
		 */
		onSort: function(evt) {
			var sortKey, sortDir, $target;

			evt.preventDefault();

			// disallow sorting while notes are synced
			if(!this.syncEnabled) {
				$target = $(evt.target);
				sortKey = $target.attr('data-sort');
				sortDir = $target.attr('data-sort-dir') || 'asc';

				$target.attr('data-sort-dir', sortDir === 'asc' ? 'desc' : 'asc');
				this.sortBy(sortKey, sortDir);
			}
		},

		/**
		 * Sort the notes by a note attribute and direction. 
		 *
		 * @method sortBy
		 * @param {String} key A Note model key.
		 * @param {String} dir A direction. Either *asc* or *desc*.
		 */
		sortBy: function(key, dir) {
			var compare;
			if(!dir || dir.toLowerCase() === 'asc') {
				compare = function(a, b) {
					return a < b ? -1 : a == b ? 0 : 1;
				};
			} else {
				compare = function(a, b) {
					return a > b ? -1 : a == b ? 0 : 1;
				};
			}
			
			var comparator_for = {
				title: function(a, b) {
					var aTitle = (a.get('title')||'').toLowerCase();
					var bTitle = (b.get('title')||'').toLowerCase();
					return compare(aTitle, bTitle);
				},
				date: function(a, b) {
					return compare(a.get('created_unix'), b.get('created_unix'));
				},
				'start-time': function(a,b) {
					var data = {};
					_.each({a:a, b:b}, function(note, key) {
						var seconds = null;
						var segment = note.get('segments') && note.get('segments').first();
						if(segment) {
							seconds = TimeConverter(segment.get('start_time')).toSeconds();
						}
						data[key] = seconds === null ? -1 : seconds;
					});
					
					return compare(data.a, data.b);
				}
			};

			if(comparator_for[key]) {
				this.collection.comparator = comparator_for[key];
				this.collection.sort();
			}
		},

		/**
		 * Event handler for refreshing the notes from the server.
		 *
		 * @method onRefresh
		 * @param {Object} evt jQuery event object
		 */
		onRefresh: function(evt) {
			var $notes = this.$('.note-annotations');
			evt.preventDefault();
			
			$notes.addClass('loading');
			
			this.collection.fetch({
				error: function(collection, response) {
					$notes.removeClass('loading');
					$notes.html('Loading failed.');
				},
				success: function(collection, response) {
					$notes.removeClass('loading');
				}
			});
		},

		/**
		 * Event handler for toggling note synchronization with the video.
		 *
		 * @method onToggleSync
		 * @param {Object} evt jQuery event object
		 */
		onToggleSync: function(evt) {
			var iconCls = { 
				sync: 'note-icon-sync', 
				unsync: 'note-icon-unsync' 
			};

			this.syncEnabled = !this.syncEnabled;
			this.updateSyncBtn();
			this.trigger('toggleSync', this.syncEnabled);
		},
		
		/**
		 * Updates the sync button icon and style based on the
		 * current sync status.
		 * 
		 * @method updateSyncBtn
		 */
		updateSyncBtn: function() {
			var iconCls = { 
				sync: 'note-icon-sync', 
				unsync: 'note-icon-unsync' 
			};

			this.syncBtnEl.
				removeClass(this.syncEnabled ? iconCls.sync : iconCls.unsync).
				addClass(this.syncEnabled ? iconCls.unsync : iconCls.sync);
		},

		/**
		 * Event handler for searching the notes.
		 *
		 * @method onSearch
		 * @param {Object} evt jQuery event object
		 */
		onSearch: function(evt) {
			var query = this.searchInputEl.val();
			evt.preventDefault();
			this.collection.search({ q: query });
		},

		/**
		 * Event handler for expanding or collapsing all notes.
		 *
		 * @method onSearch
		 * @param {Object} evt jQuery event object
		 */
		onToggleDetails: function(evt) {
			var collapsed = !this.collapsed;
			var collapsedComments = true;
			var suppressToggleEvent = true;

			_(this.views).each(function(view) {
				view.toggleDetails(collapsed, suppressToggleEvent);
				view.toggleComments(collapsedComments, suppressToggleEvent);
			});

			_(this._viewStates).each(function(state, key) {
				state.showDetails = !collapsed;
				state.showComments = false;
			});

			this.collapsed = collapsed;
			this.updateCollapseBtn();
		},

		/**
		 * Updates the collapse/expand button icon and style based on the
		 * current sync status.
		 * 
		 * @method updateSyncBtn
		 */
		updateCollapseBtn: function() {
			var iconCls = { 
				collapse: 'note-icon-collapse', 
				expand: 'note-icon-expand' 
			};

			this.collapseBtnEl.
				removeClass(this.collapsed ? iconCls.collapse : iconCls.expand).
				addClass(this.collapsed ? iconCls.expand : iconCls.collapse);
		},

		/**
		 * Callback for when a new annotation is added to the collection.
		 *
		 * @method onAddAnnotation
		 * @param {Object} model 
		 * @param {Object} collection 
		 * @param {ObjecT} options 
		 * @return undefined
		 */
		onAddAnnotation: function(model, collection, options) { 
			// Note: only rendering when the view is an annotatino because
			// sub-views are responsible for handling comments that are added
			if(model.isAnnotation()) {
				this.render();
			}
		},

		/**
		 * Render the list of annotations.
		 *
		 * @method render
		 * @return {Object} this
		 */
		render: function() {
			var el = this.$('.note-annotations');
			var views = this.createViews();

			if(views.length > 0) {
				this.renderViews(views);
				this.destroyViews(this.views);
				this.views = views;
			} else {
				el.html(this.emptyTemplate({ msg: this.emptyText }));
			}
			
			this.afterRender();
			
			return this;
		},
		
		/**
		 * Executed after rendering views.
		 * 
		 * @method afterRender
		 */
		afterRender: function() {
			this.focusNoteOnce();
		},
		
		/**
		 * Automatically focuses a note once. Used to focus
		 * a note given by the "note_id" URL query parameter
		 * and passed to the view as an option.
		 * 
		 * @method focusNoteOnce
		 */
		focusNoteOnce: function() {
			var self = this;
			var showNoteId = this.options.showNoteId;
			var selector = '#note-'+showNoteId;
			var isNote = function(model) {
				return model.id === showNoteId;
			};
			var $focus;
			
			if(typeof showNoteId !== 'undefined') {
				_.each(this.views, function(view) {
					if(view.model.id === showNoteId || _.any(view.getComments(), isNote)) {
						view.expand();
						view.expandComments();

						$focus = this.$(selector).addClass('highlight');
						if($focus.length === 1) {
							$focus.get(0).scrollIntoView();
							$focus.on('mouseover', function() {
								$focus.removeClass('highlight').addClass('unhighlight');
							});
						}
						
						// only do this once, so remove the option
						delete self.options.showNoteId; 
					}
				});
			}
		},

		/**
		 * Creates the sub-views.
		 *
		 * @method createViews
		 * @return {Array} views
		 */
		createViews: function() {
			var self = this;
			var lockComments = this.lockComments;
			var collapsed = this.collapsed;
			var notes = this.collection.getAnnotations();
			var viewStates = this._viewStates;

			var views = _.map(notes, function(note){
				var state = viewStates[note.id] || {};
				if(!state.hasOwnProperty('showDetails')) {
					state.showDetails = !collapsed;
				}
				var options = {
					state: state,
					model: note,
					lockComments: lockComments,
					parentView: self
				};
				return new VideoAnnotationView(options);
			});

			return views;
		},

		/**
		 * Renders the sub-views
		 *
		 * @method renderViews
		 * @return undefined
		 */
		renderViews: function(views) {
			var self = this;
			var maxListHeight = $(window).height() - 300;
			var el = this.$('.note-annotations');
			var newEl = $(this.make('div', { 
				'class': 'notes note-annotations'
				//'style': 'overflow: auto; max-height: ' + maxListHeight + 'px'
			}));

			var els = _.map(views, function(view) {
				view.render();
				view.on('toggle', self.onViewStateToggle, self);
				return view.el;
			});

			el.replaceWith(newEl.append(els));
		},
		
		/**
		 * Returns a specific sub-view by model id.
		 * 
		 * @method getViewByModel
		 * @param {Number} id model id
		 */
		getViewByModel: function(id) {
			return _(this.views).filter(function(view) {
				return view.model.id === id;
			});
		},

		/**
		 * Returns a specific sub-view by it's index.
		 * 
		 * @method getViewByModel
		 * @param {Number} id model id
		 */
		getViewAt: function(index) {
			return this.views[index];
		},

		/**
		 * Destroys the sub-views.
		 *
		 * @method destroyViews
		 * @param {Array} views list of views to destroy
		 * @return undefined
		 */
		destroyViews: function(views) {
			_.each(views, function(view) {
				view.destroy();
			});
		},

		/**
		 * Respondes to toggle events on sub-views. It records the
		 * state of the view so that it can be restored later if/when
		 * the views are re-rendered.
		 * 
		 * @method onViewStateToggle
		 * @param {Object} view the view that has been toggled
		 * @param {Number} model_id the id of the model attached to the view
		 * @param {Boolean} state the toggle state
		 * @return undefined
		 */
		onViewStateToggle: function(view, model_id, state) {
			if((typeof model_id === 'number' || typeof model_id == 'string') && model_id !== '') {
				this._viewStates[model_id] = view.state;
			}
			//console.log('toggle', 'view:', view, 'model_id:', model_id, 'state:', state, 'viewState:', this._viewStates[model_id]);
		}
	});

	App.views.VideoNotesView = VideoNotesView;
})(Catool);

/**
 * A class for managing the video annotation view.
 *
 * This class should be instantiated and bootstrapped
 * with data from /videos/view/{id}. 
 *
 * @class VideoAnnotationView
 * @namespace Catool.views
 * @constructor
 */
(function(App) {
	// dependencies
	var Video = App.models.Video;
	var Notes = App.collections.Notes;
	var View = App.View;
	var VideoNotesView = App.views.VideoNotesView;
	var VideoNoteFormView = App.views.VideoNoteFormView;
	var VideoPlayerView = App.views.VideoPlayerView;
	var NoteSyncModel = App.utils.NoteSyncModel;

	var VideoAppView = View.extend({

		/**
		 * Initializes the view. 
		 *
		 * @method initialize
		 * @param {Object} options Hash of options.
		 */
		initialize: function() {
			var options = this.options;
			var data = options.data;
			var config = options.config;
			var video = new Video(data.video);
			var notes = new Notes(data.notes, { parse: true, targetModel: video });

			var videoPlayerView = new VideoPlayerView({ 
				el: this.$('.notes-video-player'),
				playerId: 'notes-video-player-1',
				model: video
			});

			var videoNoteFormView = new VideoNoteFormView({
				el: this.$('.note-form-view'),
				collection: notes,
				video: video,
				player: videoPlayerView
			});
			
			videoNoteFormView.on('cancel', function() {
				$('a[data-target=".notes-view"]').tab('show');
			});

			var videoNotesView = new VideoNotesView({
				el: this.$('.notes-view'),
				collection: notes,
				sortConfig: config.sortNotesBy,
				showNoteId: config.noteId,
				highlightAdmins: config.highlightAdmins,
				lockComments: (App.user.isAdmin ? false : config.lockComments),
				lockAnnotations: (App.user.isAdmin ? false : config.lockAnnotations),
				syncEnabled: config.syncAnnotations,
				syncModel: new NoteSyncModel({
					player: videoPlayerView
				})
			});

			this.videoNoteFormView = videoNoteFormView;
			this.videoPlayerView = videoPlayerView;
			this.videoNotesView = videoNotesView;

			this.views = [ videoNotesView, videoNoteFormView, videoPlayerView ];

			this.render();
		},

		/**
		 * Render the view.
		 *
		 * @method render
		 * @return {Object} this
		 */
		render: function() {
			_(this.views).each(function(view) {
				view.render();
			});
			this.afterRender();
			return this;
		},

		/**
		 * Performs additional setup tasks After the view has been rendered.
		 *
		 * @method afterRender
		 */
		afterRender: function() {
			var self = this;

			// enable tooltips using bootstrap plugin
			$('*[rel="tooltip"]').tooltip();

			// activate/deactive player UI controls depending on the tab 
			$('#new_comment_tab').on('click', function() {
				self.videoPlayerView.clearSegmentHandler();
				self.videoPlayerView.showRangeSlider();
				self.videoPlayerView.syncRangeSlider();
			});
			$('#current_comments_tab').on('click', function() {
				self.videoPlayerView.hideRangeSlider();
			});

			return this;
		}
	});
	
	App.views.VideoAppView = VideoAppView;
})(Catool);
// @file app/scripts.js
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
/**
 * Scripts are standalone bits of functionality, not part of an "app"
 * structure. This defines basic utilities to setup scripts so that they
 * are testable.
 * 
 * Usage:
 * 		Catool.defineScript('my-script', function() {
 * 			// do stuff
 * 		});
 * 
 * 		jQuery.ready(Catool.defineScript('my-script'));
 * 		$(Catool.defineScript('my-script'));
 */
(function(global) {
	global.Catool = global.Catool || {};

	/**
	 * Sets and gets a script, which is just a function associated with a name.
	 * The intended use is to define the script in an external js file then load
	 * it from the view and pass it to the jQuery.ready() function. 
	 */
	global.Catool.defineScript = function(name, fn) {
		this.scripts = this.scripts || {};
		this.scripts[name] = fn;
	};

	global.Catool.script = function(name, config) {
		var fn = this.scripts[name];
		config = config || {};

		if(!this.scripts.hasOwnProperty(name)) {
			throw new Error("No such script: " + name);
		}

		return function() {
			var args = Array.prototype.slice.call(arguments);
			if(config.hasOwnProperty('data')) {
				args.push(config.data);
			}
			fn.apply(config.scope || global, args);
		};
	};

})(window);

/**
 * Script for the video add/edit screen.
 *
 * Description:
 * When the validate button is pressed on the form, the value of the URL field
 * is used to instantiate a new video player and begin playback. As soon as
 * metadata is retrieved, the duration field is updated.
 */
Catool.defineScript('video-edit', function($) {
	var _V_ = window._V_; // dependency on videojs

	var videoId = 'video-player-1';
	var player; // holds reference to player when ready
	var playerOptions = { techOrder: ['html5', 'flash', 'youtube'] };

	//----------------------------------------
	// Cached jQuery elements

	var $url = $('#resource_url');
	var $controlGroup = $('#resource-url-control-group');
	var $duration = $('#resource_duration');
	var $container = $('#video-player-container-1');
	var $fileType = $('#resource_file_type');
	var $playerErrorModal = $('#playerErrorModal');
	var $videoDeleteModal = $('#videoDeleteModal');
	var $deleteButton = $('#delete_video_button');
	var $validateVideoButton = $('#validate_video');

	//----------------------------------------
	// Utility functions
	
	var convertYoutubeShort2Long = function(url) {
		var re = /^http:\/\/youtu\.be\/(.*)$/;
		var matches, youtube_id;

		if(re.test(url)) {
			matches = re.exec(url);
			youtube_id = matches[1];
			url = 'http://www.youtube.com/watch?v=' + youtube_id;
		}

		return url;
	};

	var showLinkValidates = function(isValid) {
		if(isValid) {
			if($controlGroup.hasClass('error')) {
				$controlGroup.removeClass('error');
			}
			$controlGroup.addClass('success');
		} else {
			if($controlGroup.hasClass('success')) {
				$controlGroup.removeClass('success');
			}
			$controlGroup.addClass('error');
		}
	};

	var onDurationChange = function() {
		var value = player.duration();
		if(isValidDuration(value)) {
			value = Math.floor(value);
			$duration.val(value);
		}
	};

	var isValidDuration = function(val) {
		return !isNaN(val) && val > 0;
	};

	var checkDuration = function() {
		var value = parseInt($duration.val().trim(), 10);
		var isValid = isValidDuration(value);
		showLinkValidates(isValid);
	};

	var onError = function(evt) {
		$playerErrorModal.modal('show');
	};

	var onClickValidate = function() {
		var sourceUrl = convertYoutubeShort2Long($url.val().trim());
		var type = $fileType.val();
		var video = { type: type, src: sourceUrl };

		$duration.val(''); // reset duration
		if(type && sourceUrl) {
			$url.val(sourceUrl);
			$container.show();
			player.src(video);
			player.play();
		} else {
			player.pause();
			$container.hide();
			showLinkValidates(false);
		}
	};

	var onDetectType = function() {
		var autoDetect = {
			'video/flv': /\.flv$/,
			'video/mp4': /(?:\.mp4|\.m4v)$/,
			'video/youtube': /^(?:http:\/\/www\.youtube\.com\/watch\?v=)|(?:http:\/\/youtu\.be)/
		};
		var value = $url.val().trim();
		var type, detected = '';

		for(type in autoDetect) {
			if(autoDetect.hasOwnProperty(type)) {
				if(autoDetect[type].test(value)) {
					detected = type;
					break;
				}
			}
		}

		$fileType.val(detected);
	};
	
	var onDeleteVideo = function(evt) {
		evt.preventDefault();
		$videoDeleteModal.modal('show');
	};
	
	var initPlayer = function(ready) {
		_V_(videoId, playerOptions, function() {
			player = this;
			player.addEvent('durationchange', onDurationChange);
			player.addEvent('play', onDurationChange);
			player.addEvent('error', onError);

			if(ready) {
				ready.apply(player, arguments);	
			}
		});
	};

	var onCheckDurationValid = function() {
		checkDuration();
		setTimeout(onCheckDurationValid, 750);
	};

	//----------------------------------------
	// Setup the form event handlers and initialize the video player

	$url.on('keyup', onDetectType);

	$deleteButton.on('click', onDeleteVideo);

	$validateVideoButton.on('click', function() {
		onDetectType.apply(this, arguments);
		onClickValidate.apply(this, arguments);
	});

	// only execute this handler first time "validate" button is clicked
	$validateVideoButton.one('click', onCheckDurationValid); 

	initPlayer();
});

/**
 * Script for the collection add/edit screen.
 */
Catool.defineScript('collection-edit', function($) {
	$('#delete_collection_button').on('click', function(evt) {
		evt.preventDefault();
		$('#collectionDeleteModal').modal('show');
	});
});

/**
 * Script for the collection permissions screen.
 */
Catool.defineScript('collection-edit-permissions', function($, data) {
	// initialize data tables and modals
	$('#users_table').dataTable({
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": true,
		"bSort": true,
		"aaSorting": [[1, "asc" ]], // sort by name
		"bInfo": false,
		"bAutoWidth": false
	});

	// initialize action butto handlers
	$('#users_table .actions > .btn').on('click', function(evt) {
		evt.preventDefault();
		var id = $(this).data('id');
		var action = $(this).data('action');
		var rec = data[id];

		switch(action) {
			case 'edit':
				$('#user_collection_edit_id').val(rec.UserCollection.id);
				$('#user_collection_edit_role input:radio').each(function(idx, el) {
					$(this).attr('checked', this.value == rec.Role.id);
				});
				$('#user_collection_edit_name').html(rec.User.name);
				$('#user_collection_edit_email').html(rec.User.email);
				$('#editUserModal').modal('show');
				break;
			case 'delete':
				$('#user_collection_delete_id').val(rec.UserCollection.id);
				$('#user_collection_delete_name').html(rec.User.name);
				$('#deleteUserModal').modal('show');
				break;
			default:
				break;
		}
	});
});

/**
 * Script for the site permissions screen.
 */
Catool.defineScript('site-edit-permissions', function($, data) {
	// initialize data tables and modals
	$('#users_table').dataTable({
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": true,
		"bSort": true,
		"aaSorting": [[1, "asc" ]], // sort by name
		"bInfo": false,
		"bAutoWidth": false
	});

	// initialize action butto handlers
	$('#users_table .actions > .btn').on('click', function(evt) {
		evt.preventDefault();
		var id = $(this).data('id');
		var action = $(this).data('action');
		var rec = data[id];

		switch(action) {
			case 'edit':
				$('#user_edit_id').val(rec.User.id);
				$('#user_edit_role input:radio').each(function(idx, el) {
					$(this).attr('checked', this.value == rec.Role.id);
				});
				$('#user_edit_name').val(rec.User.name);
				$('#user_edit_email').val(rec.User.email);
				$('#editUserModal').modal('show');
				break;
			case 'delete':
				$('#user_delete_id').val(rec.User.id);
				$('#user_delete_name').html(rec.User.name);
				$('#deleteUserModal').modal('show');
				break;
			default:
				break;
		}
	});
});

/**
 * Script for adding data table enhancement.
 */
Catool.defineScript('collection-posts', function($) {
	$('#posts_table').dataTable({
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": true,
		"bSort": true,
		"aaSorting": [[1, "desc" ]], // sort by date
		"bInfo": false,
		"bAutoWidth": false
	});
});
