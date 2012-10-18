# INSTALL

## EXAMPLE CONFIGURATION FOR A STANDARD LAMP ENVIRONMENT

*Disclaimer*: the configuration provided below is only an example. You should adapt this for your own specific environment (Ubuntu Linux, MAC OS X, etc). Do _not_ use this for production.

* Get the source either by downloading and unzipping a ZIP file (see the ZIP icon on github) or by cloning the repository:
  
```sh
git clone git@github.com:arthurbarrett/Catool.git Catool
```
	 
* Setup your Apache web server by adding the following section to your Apache vhosts config <code>conf/apache/extra/httpd-vhosts.conf</code>:
	 
```apache
<VirtualHost *:80>
	ServerName catool.localhost # your server hostname
	DocumentRoot "/Applications/MAMP/htdocs/Catool/app/webroot" # app/webroot should be public
	<Directory /Applications/MAMP/htdocs/Catool/app/webroot>
		Options All
		AllowOverride All
		Order deny,allow
		Deny from all
		Allow from 127.0.0.1 localhost
	</Directory>
</VirtualHost>
```
	 
* Make sure that the Include line for vhosts is uncommented in <code>conf/apache/httpd.conf</code>:
	 
```apache
# Virtual hosts
Include /Applications/MAMP/conf/apache/extra/httpd-vhosts.conf
```
 
* If you are hosting the application on your local machine, update your <code>/etc/hosts</code> file to match the *ServerName*:
 
```sh
127.0.0.1	catool.localhost
```

* Start the Apache & MySQL services.
* Using phpMyAdmin or another MySQL admin tool, setup a database, username, and password for the application.
* Run the application web installer by pointing your web browser to <code>install.php</code> (i.e. *http://catool.localhost/install.php*). You will be prompted for your MySQL database connection information.
* Follow the instructions on the web installer to login to the application as a super user.

## COMMON PROBLEMS
* File permissions need to be setup such that:
	* <code>Config</code> must be writable by the web server (during the install so configs can be saved).
	* <code>tmp</code> must be writable by the web server for logging, caching, etc.
* If configuration settings are changed by hand, make sure to clear cache files in <code>tmp/cache</code>, otherwise you may get an *Internal Server Error*.


## DEPENDENCIES

The following third-party packages and libraries are bundled with this application:

* CakePHP. _PHP MVC Web Framework. MIT license._
* Phpseclib. _PHP Secure Communications Library. MIT license._
* LightOpenID. _PHP library for easy openid authentication. MIT license._
* Less. _CSS Framework. MIT license._
* Twitter Bootstrap. _HTML, CSS, and JS components. Apache license 2.0_
* JQuery. _JS DOM utility library. GPL/MIT license._
* VideoJS. _HTML5/flash media player. LGPLv3 license._
* Backbone.js. _JS MVC framework. GPL/MIT license._
* Underscore.js _JS functional programming utilities. MIT license._
* Moment.js: _JS date library. MIT license._
