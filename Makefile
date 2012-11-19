##################################################
# This Makefile is primarily used to build JS and CSS assets.
# 
# It is used in combination with a build config, build.php, 
# that groups js and css into logical packages that are
# then concatenated into a single file (i.e. for debugging)
# and then minified and copied to web-accessible locations.
#
# The YUI Compressor library is used for minification, so
# Java must be installed. 
#
# Usage: 
#	make
#	make clean

app: combine minify copy

combine:
	php build/cat.php js-app > build/app.debug.js
	php build/cat.php js-lib > build/lib.debug.js
	php build/cat.php css-app > build/app.debug.css

minify:
	java -jar build/yuicompressor-2.4.7.jar build/app.debug.js > build/app.min.js
	java -jar build/yuicompressor-2.4.7.jar build/lib.debug.js > build/lib.min.js
	java -jar build/yuicompressor-2.4.7.jar build/app.debug.css > build/app.min.css

copy:
	cp -v build/*.js app/webroot/js/build
	cp -v build/*.css app/webroot/css # relative paths to images must be preserved

clean: 
	rm -fv app/webroot/js/build/*.js *.js
	rm -fv app/webroot/css/app.debug.css app/webroot/css/app.min.css *.css 

##################################################
# Generate static or live docs using yuidoc
# (must be installed on your system to work).
#
# Usage:
#	make docs
#	make livedocs

docs:
	yuidoc -o docs app

livedocs:
	yuidoc --server 5000 app
