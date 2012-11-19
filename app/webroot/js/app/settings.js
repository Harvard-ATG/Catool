// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
(function(App) {
	/**
	 * Defines global, application-wide settings for the wysihtml5 editor.
	 * 
	 * The wysihtml5 editor is a jQuery plugin.
	 *
	 * See also: https://github.com/xing/wysihtml5
	 */
	var wysihtml5Config = {
		"font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
		"emphasis": true, //Italics, bold, etc. Default true
		"lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
		"html": true, //Button which allows you to edit the generated HTML. Default false
		"link": true, //Button to insert a link. Default true
		"image": true, //Button to insert an image. Default true,
		"color": false //Button to change color of font  
	};

	App.settings = _.extend(App.settings, {
		wysihtml5Config: wysihtml5Config
	});

})(Catool);
