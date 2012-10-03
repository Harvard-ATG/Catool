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
