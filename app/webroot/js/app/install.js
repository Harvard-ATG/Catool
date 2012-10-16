// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
(function(global) {
	global.Catool = global.Catool || {};
	
	var View = Backbone.View.extend({});
	var Model = Backbone.Model.extend({});
	var Collection = Backbone.Collection.extend({});

	/**
	 * Install task model
	 *
	 * Keeps track of the state of each task and handles logic for executing
	 * tasks. Tasks are executed by submitting AJAX queries and examining
	 * the response for success or failure.
	 *
	 * @constructor
	 */
	var InstallTaskModel = Model.extend({
		idAttribute: 'name',
		initialize: function(attributes) {
			this.attributes = _.extend({
				name: '', // task identifier
				description: '', // displayed in task list
				taskUrl: null, // url to invoke action
				requireInput: false, // task requires user input?
				success: null, // task done?
				visible: false, // task displayed?
				loading: false, // task loading?
				message: '-', // general status message
				log: '', // detailed log messages
				error: '' // detailed error message
			}, attributes);
		},
		url: function() {
			// override to use task url
			return this.get('taskUrl');
		},
		isVisible: function() {
			return this.get('visible') ? true : false;
		},
		isDone: function() {
			return this.get('success') === true;
		},
		isError: function() {
			return this.get('success') === false;
		},
		isLoading: function() {
			return this.get('loading') ? true : false;
		},
		hasLogMessage: function() {
			var log = this.get('log');
			return (_.isArray(log) || _.isString(log)) && log.length > 0;
		},
		hasErrorMessage: function() {
			var error = this.get('error');
			return (_.isArray(error) || _.isString(error)) && error.length > 0;
		},
		show: function() {
			this.set('visible', true);
		},
		hide: function() {
			this.set('visible', false);
		},
		execute: function() {
			if(!this.url()) {
				return false;
			}

			var ajaxSettings = {
				url: this.url(),
				error: _.bind(this.onAjaxError, this),
				success: _.bind(this.onAjaxSuccess, this),
				beforeSend: _.bind(this.onAjaxBeforeSend, this),
				complete: _.bind(this.onAjaxComplete, this),
				type: 'GET'
			};

			if(this.get('requireInput') && !this.get('data')) {
				this.trigger('taskinput', this);	
				return false;
			} else if(this.get('requireInput') && this.get('data')) {
				ajaxSettings.type = 'POST';
				ajaxSettings.data = this.get('data');
			}

			$.ajax(ajaxSettings);

			return true;
		},
		onAjaxError: function(jqXHR, textStatus, errorThrown) {
			this.set({ 
				success: false,
				message: textStatus||'',
				error: errorThrown||''
			});

			this.trigger('taskerror', this);
		},
		onAjaxSuccess: function(data, textStatus, jqXHR) {
			data.success = data.success || false;

			_(['error', 'log']).each(function(key) {
				if(data[key] && _.isArray(data[key])) {
					data[key] = data[key].join("\n\n");
				} else if(!data[key]) {
					data[key] = '';
				}
			});

			this.set(data);
			this.trigger(data.success ? 'taskdone' : 'taskerror', this);
		},
		onAjaxBeforeSend: function() {
			this.set('loading', true);
		},
		onAjaxComplete: function() {
			this.set('loading', false);
		}
	});

	/**
	 * Install task collection
	 *
	 * Responsible for holding a queue of tasks and executing the next one
	 * in sequence, or signaling that all tasks are done.
	 *
	 * @constructor
	 */	
	var InstallTasksCollection = Collection.extend({
		model: InstallTaskModel,
		initialize: function(models, options) {
			this.add([{
				name: 'tempdirs',
				description: 'Temporary directory setup for logging, caching, etc',
				visible: true,
				taskUrl: '?action=setup_cake'
			},{
				name: 'database',
				description: 'MySQL Database configuration',
				taskUrl: '?action=configure_database',
				requireInput: true
			},{
				name: 'schema',
				description: 'Schema setup',
				taskUrl: '?action=create_schema'
			}]);

			this.curTaskIdx = 0;
		},
		mapVisible: function(callback) {
			this.map(function(task) {
				if(task.isVisible()) {
					return callback.apply(this, arguments);
				}
			});
		},
		incrementTask: function() {
			this.curTaskIdx++;
		},
		getNext: function() {
			return this.at(this.curTaskIdx);
		},
		doNext: function() {
			var next = this.getNext();
			if(next) {
				if(!next.isDone() && !next.isError()) {
					next.show();
					next.execute();
				}
			} else {
				this.trigger("taskscomplete");
			}
		}
	});

	/**
	 * View for an install task.
	 *
	 * Responsible for displaying the state of a task.
	 *
	 * @constructor
	 */		
	var InstallTaskView = View.extend({
		tagname: 'li',
		className: 'row task alert',
		events: { 
			'click .js-header' : 'onTaskClick'
		},
		initialize: function(options) {
			this.model.on('change', this.render, this);
		},
		render: function() {
			var template = $('#InstallTaskViewTemplate').html();
			var data = this.model.toJSON();
			data.success = this.getStatusMessage();

			this.$el.html(_.template(template, data));

			if(this.model.isVisible()) {
				this.$el.removeClass('hide');
			} else {
				this.$el.addClass('hide');
			}

			if(this.model.isLoading()) {
				this.$el.removeClass('alert-error');
				this.$el.removeClass('alert-success');
				this.$el.addClass('loading');
			} else {
				this.$el.removeClass('loading');
				if(this.model.isError()) {
					this.$el.addClass('alert-error');
				} else if(this.model.isDone()) {
					this.$el.addClass('alert-success');
				}
			}
		},
		onTaskClick: function() {
			if(this.model.hasLogMessage()) {
				this.$('.js-log').toggleClass('hide');
			}
			if(this.model.hasErrorMessage()) {
				this.$('.js-error').toggleClass('hide');
			}
		},
		getStatusMessage: function() {
			var success = this.model.get('success');
			if(success === true) {
				return 'OK';
			} else if(success === false) {
				return 'ERROR';
			}
			return '-';
		}
	});

	/**
	 * Factory method to create a view object
	 */
	InstallTaskView.create = function(model) {
		var options = { model: model };
		if(model.get('name') === 'database') {
			return new InstallDatabaseTaskView(options);
		}
		return new InstallTaskView(options);
	};

	/**
	 * View for a database install task.
	 *
	 * Responsible for displaying the state of a database install task,
	 * and more importantly, prompting the user to enter the database
	 * configuration.
	 *
	 * @constructor
	 */	
	var InstallDatabaseTaskView = InstallTaskView.extend({
		events: {
			'click .js-submit-btn' : 'onSubmit',
			'click .js-header' : 'onTaskClick'
		},
		data: {
			host: 'localhost',
			port: '',
			login: '',
			password: '',
			database: '',
			prefix: ''
		},
		taskInput: false,
		initialize: function(options) {
			InstallTaskView.prototype.initialize.call(this, options);

			this.model.on('taskinput taskerror', this.onTaskShowInput, this);
			this.model.on('taskdone', this.onTaskHideInput, this);
		},
		render: function() {
			InstallTaskView.prototype.render.call(this);
			if(this.taskInput) {
				this.renderForm();
			}
		},
		renderForm: function() {
			var template, data = {};

			data = _.clone(this.data);
			data = _.extend(data, this.model.get('data') || {});

			template = $('#InstallTaskDatabaseForm').html();

			this.$el.append(_.template(template, data));
		},
		onTaskShowInput: function() {
			this.taskInput = true;
			this.render();
		},
		onTaskHideInput: function() {
			this.taskInput = false;
		},
		onSubmit: function(e) {
			var data = {}, self = this;

			e.preventDefault();
			_.each(this.data, function(value, key) {
				data[key] = this.$('input[name='+key+']').val();
			});

			this.model.set('data', data);
			this.model.execute();
		}
	});

	/**
	 * View a list of tasks.
	 *
	 * Responsible for displaying a list of tasks, prominently displaying
	 * any errors that occur, and initiating the first task.
	 *
	 * @constructor
	 */	
	var InstallTasksView = View.extend({
		className: 'tasks',
		initialize: function(options) {
			this.tasks = new InstallTasksCollection();
			this.tasks.on('taskdone', this.onTaskDone, this);
			this.tasks.on('taskerror', this.onTaskError, this);
			this.tasks.on('taskscomplete', this.onAllTasksComplete, this);

			this.views = this.getViews();
			this.beginTasks();
		},
		render: function() {
			var template = _.template($('#InstallTasksViewTemplate').html());
			
			this.$el.html(template);

			_.each(this.views, function(view) {
					this.$el.append(view.render().el);
			}, this);
		},
		getViews: function() {
			return this.tasks.map(function(task) {
				return InstallTaskView.create(task);	
			});
		},
		beginTasks: function() {
			if(this.tasks.size() > 0) {
				this.tasks.doNext();
			}
		},
		onTaskDone: function(task) {
			this.tasks.incrementTask();
			this.tasks.doNext();
		},
		onTaskError: function(task) {
			var $error = $('#InstallTaskError');
			var template = _.template($('#InstallTaskErrorTemplate').html(), task.toJSON());

			$error.find('.js-error').html(template);
			$error.show();
		},
		onAllTasksComplete: function() {
			var template = _.template($('#InstallTasksCompleteTemplate').html(), {});
			this.$el.append(template);
		}
	});

	global.Catool.InstallTasksView = InstallTasksView;
})(window);
