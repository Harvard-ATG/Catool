# INSTALL

## EXAMPLE SETUP FOR UBUNTU LINUX 12.04

TODO

## EXAMPLE SETUP FOR OS X MAMP ENVIRONMENT (MAMP v2.0.5)

Disclaimer: the configuration provided below is intended for a sandbox environment only. Do not use this for production.

* Get the source
	
```sh
cd /Applications/MAMP/htdocs
git clone git@github.com:arthurbarrett/Catool.git Catool
```
	 
* Add the following section to your Apache vhosts config <code>/Applications/MAMP/conf/apache/extra/httpd-vhosts.conf</code>:
	 
```apache
<VirtualHost *:80>
	ServerName catool.localhost
	DocumentRoot "/Applications/MAMP/htdocs/Catool/app/webroot"
	<Directory /Applications/MAMP/htdocs/Catool/app/webroot>
		Options All
		AllowOverride All
		Order deny,allow
		Deny from all
		Allow from 127.0.0.1 localhost
	</Directory>
</VirtualHost>
```
	 
* Uncomment the Include line for vhosts in <code>/Applications/MAMP/conf/apache/httpd.conf</code>:
	 
```apache
# Virtual hosts
Include /Applications/MAMP/conf/apache/extra/httpd-vhosts.conf
```
 
* Update your <code>/etc/hosts</code>:
 
```sh
127.0.0.1	catool.localhost
```

* Open the MAMP control panel. Go to _Preferences..._ then _Ports_... and click _Set to default Apache and MySQL ports_. Click OK.
* Click Start Servers in the MAMP control panel. The Apache Server and MySQL Server status should become green.
* Run the following shell script to install the application (setup config, create schema, etc):

```sh
/Applications/MAMP/htdocs/Catool/scripts/install_mamp.sh
```

Note: you will be prompted for the database configuration. You should enter two database configurations, one called "default" and another called "test." After each database configuration, the result should look like this:

<pre>
---------------------------------------------------------------
The following database configuration will be created:
---------------------------------------------------------------
Name:         default
Datasource:       Mysql
Persistent:   false
Host:         localhost
Port:         8889
User:         catool
Pass:         catool
Database:     catool
---------------------------------------------------------------
</pre>

<pre>
---------------------------------------------------------------
The following database configuration will be created:
---------------------------------------------------------------
Name:         test
Datasource:       Mysql
Persistent:   false
Host:         localhost
Port:         8889
User:         catool_test
Pass:         catool_test
Database:     catool_test
---------------------------------------------------------------
</pre>

* Open <code>/Applications/MAMP/htdocs/Catool/app/Config/core.php</code> and modify the config values for *Security.salt* and *Security.cipherSeed*.

* If there were no errors in the install process, you should now be able to open http://catool.localhost/ in your web browser, at which point you will be prompted to login.

* Note: if you want to reset/reinstall the application, all you have to do is this:

```sh
cd /Applications/MAMP/htdocs/Catool/app
Console/cake install app  # Reinstall database and other setup tasks
Console/cake install test # Runs the unit test suite
Console/cake populate # **DEV ONLY** Populates application with FAKE/TEST data 
```