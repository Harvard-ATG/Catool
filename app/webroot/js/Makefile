##################################################
# For Building and compressing the app js
#
# Usage: 
#	make
#	make clean

app: combinejs minifyjs

combinejs:
	php -r '$$js = include "build.php"; foreach($$js["app"] as $$name) { readfile($$name)!==false || die("**BUILDERROR> $$name"); }' > build/app.js
	php -r '$$js = include "build.php"; foreach($$js["lib"] as $$name) { readfile($$name)!==false || die("**BUILDERROR> $$name"); }' > build/lib.js

minifyjs:
#	uglifyjs -nc build/app.js > build/app.min.js
	java -jar build/yuicompressor-2.4.7.jar build/app.js > build/app.min.js
	java -jar build/yuicompressor-2.4.7.jar build/lib.js > build/lib.min.js

clean: 
	rm -f build/*.js

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
