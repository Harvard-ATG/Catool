#!/bin/bash

APP_DIR=/Applications/MAMP/htdocs/Catool
MYSQL_ROOT_PASSWORD=root
INSTALL_STEP=0

print_step() {
	let INSTALL_STEP++
	echo "[$INSTALL_STEP]: $1"
	sleep 1;
}

cd $APP_DIR

print_step "Creating the databases and users"
/Applications/MAMP/Library/bin/mysql -uroot -p$MYSQL_ROOT_PASSWORD -v < $APP_DIR/app/Config/Schema/create_database.sql
/Applications/MAMP/Library/bin/mysql -uroot -p$MYSQL_ROOT_PASSWORD -v -e 'show databases; select User from mysql.user;'

#----
# There's an issue with the default pear.conf in MAMP that results in this error
# if you try to do a "pear upgrade":
#
## Notice: unserialize(): Error at offset 276 of 1133 bytes in Config.php on line 1050
## ERROR: The default config file is not a valid config file or is corrupted.</code>
#
# The solution is to either delete the pear.conf and have pear re-create it, or fix the error 
# in the serialized PHP. Running the perl command below should fix the issue. For more info
# on the issue, see the link below:
# 
# http://stackoverflow.com/questions/8375327/mamp-how-to-upgrade-pear
#----
print_step "Fixing a problem with MAMP's pear.conf"
/usr/bin/perl -pi -e 's/"php_dir";s:44/"php_dir";s:43/' /Applications/MAMP/bin/php/php5.3.6/conf/pear.conf

print_step "Upgrading pear and installing PHP Unit, which is required for running the unit tests"
/Applications/MAMP/bin/php/php5.3.6/bin/pear upgrade
/Applications/MAMP/bin/php/php5.3.6/bin/pear channel-discover pear.symfony-project.com
/Applications/MAMP/bin/php/php5.3.6/bin/pear channel-discover pear.phpunit.de 
/Applications/MAMP/bin/php/php5.3.6/bin/pear install pear.phpunit.de/PHPUnit

print_step "Making writable temporary directories for the application"
mkdir -pv $APP_DIR/app/tmp/cache/models $APP_DIR/app/tmp/cache/persistent $APP_DIR/app/tmp/cache/views $APP_DIR/app/tmp/logs/tmp/sessions $APP_DIR/app/tmp/tests
chgrp -Rv admin $APP_DIR/app/tmp && chmod -R g+ws $APP_DIR/app/tmp

print_step "Configuring your environment for CakePHP's console utility"
export PATH=/Applications/MAMP/bin/php/php5.3.6/bin:$PATH 
export PATH=$APP_DIR/app/Console:$PATH

print_step "Installing the application. You will be prompted to enter the DB username and password: catool/catool"
$APP_DIR/app/Console/cake install app

print_step "Running the application unit tests"
$APP_DIR/app/Console/cake install test

echo ""
echo "-------------------------------"
echo "Installation process completed."
echo "-------------------------------"
echo ""
echo "NOTE: to use the 'cake' console, you must update" 
echo "your PATH environment variable to use MAMP's PHP:"
echo ""
echo "export PATH=$APP_DIR/app/Console:/Applications/MAMP/bin/php/php5.3.6/bin:\$PATH"
