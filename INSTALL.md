# INSTALL

* Get the source <code>git clone git@github.com:Harvard-ATG/Catool.git Catool</code>
* Setup your web server vhost and point the document root to *app/webroot*.
* Run the web installer from the document root:  *install.php*. You will be prompted for your database connection information.
* Alternatively, you can run the install from the command line using the Cake console utility:

```sh
cd Catool/app
Console/cake install app  # setup database/schema
Console/cake install test # run tests
```

However, if you install with the console command, you have to manually create the database config and temporary directories for logging, caching, etc. To create the database config, copy *app/Config/database.php.default* to *app/Config/database.php* and modify the settings for your database. Make sure your *app/tmp* directory has been created and is writable by the web server.
