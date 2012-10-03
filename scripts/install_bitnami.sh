#!/bin/bash

BASE_DIR=/Applications/Bitnami
APP_DIR=$BASE_DIR/apps/Catool
MYSQL_ROOT_PASSWORD=root
MYSQL_BIN=$BASE_DIR/mysql/bin/mysql
PHP_BIN=$BASE_DIR/php/bin/php
PEAR_BIN=$BASE_DIR/php/bin/pear
CAKE_BIN=$APP_DIR/app/Console/cake
INSTALL_STEP=0

print_step() {
	let INSTALL_STEP++
	echo "[$INSTALL_STEP]: $1"
	sleep 1;
}

cd $APP_DIR

print_step "Creating databases and users"
$MYSQL_BIN -uroot -p$MYSQL_ROOT_PASSWORD -v < $APP_DIR/app/Config/Schema/create_database.sql
$MYSQL_BIN -uroot -p$MYSQL_ROOT_PASSWORD -v -e 'show databases; select User from mysql.user;'

print_step "Upgrading pear and installing PHP Unit, which is required for running the unit tests"
$PEAR_BIN upgrade
$PEAR_BIN channel-discover pear.symfony-project.com
$PEAR_BIN channel-discover pear.phpunit.de 
$PEAR_BIN install pear.phpunit.de/PHPUnit

print_step "Making tmp directories for the application"
mkdir -pv $APP_DIR/app/tmp/cache/models $APP_DIR/app/tmp/cache/persistent $APP_DIR/app/tmp/cache/views $APP_DIR/app/tmp/logs/tmp/sessions $APP_DIR/app/tmp/tests
chgrp -Rv admin $APP_DIR/app/tmp && chmod -R g+ws $APP_DIR/app/tmp

print_step "Configuring your environment for CakePHP's console utility"
export PATH=$BASE_DIR/php/bin:$PATH

print_step "Installing the application. You will be prompted to enter the DB username and password: catool/catool"
$CAKE_BIN install app

print_step "Running the application unit tests"
$CAKE_BIN install test

echo "-------------------------------"
echo ""
echo "NOTE: To use the cake console, please update your PATH env:"
echo "export PATH=$APP_DIR/app/Console:$BASE_DIR/php/bin:\$PATH"
echo ""
echo "-------------------------------"
echo "Installation process completed."
echo "-------------------------------"
