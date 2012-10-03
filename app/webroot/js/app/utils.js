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

