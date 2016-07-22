## Ensure dependencies are installed

Our reference system:

- Ubuntu 14.04.4 LTS
- PHP 5.5.9
- MySQL 5.5.49

Approximate installation for Ubuntu:

    sudo apt-get install apache2
    sudo apt-get install mysql-client mysql-server
    sudo apt-get install php5-cli php5-common php5-curl php5-dev php5-gd php5-mysql libapache2-mod-php5 php-pear
    sudo apt-get install zip
    sudo pear install MDB2-2.5.0b4
    sudo pear install MDB2_Driver_mysqli-1.5.0b4
    sudo service apache2 restart


## Install the Archon code (dev version)

    mkdir -p /var/www/archon/{htdocs,log}
    cd /var/www/archon/htdocs
    wget https://github.com/LibraryHost/archon/archive/aup-tr1.tar.gz
    tar xfvz aup-tr1.tar.gz
    mv archon-aup-tr1/* ./


## Change settings in `config.inc.php`

    $_ARCHON->db->Login = 'archon';
    $_ARCHON->db->Password = 'password';
    $_ARCHON->db->DatabaseName = 'archon';

## Configure Apache

Example configuration:

`/etc/apache2/sites-enabled/<name>.conf`:


    <VirtualHost *:80>
            ServerName  <name>.libraryhost.com
            DirectoryIndex index.php
    
            DocumentRoot /var/www/<name>/htdocs
            LogLevel warn
    
            ErrorLog  /var/www/<name>/log/error.log
            CustomLog /var/www/<name>/log/access.log combined
    </VirtualHost>


## Prepare the Archon database and user

On the new host:

    $ mysql -u <username> -p <databasename>

At the prompt, type the following to load the database:

    mysql> CREATE DATABASE <databasename>;
    mysql> CREATE USER <username>@localhost IDENTIFIED BY "<password>";
    mysql> GRANT ALL ON <databasename>.* TO <username>@localhost;
    mysql> FLUSH PRIVILEGES;


## Run the Archon web installer **or** migrate your existing database

The Archon web installer sets up the tables and initial data Archon needs to be
functional. You should only run the installer if you are not migrating an
existing database.

### (Optional) Run the Archon web installer

First, enable the installer:

    mv packages/core/install/install.php_ packages/core/install/install.php

Use Archon's web interface to complete the installation process. When finished, disable the installer:

    mv packages/core/install/install.php packages/core/install/install.php_

### (Optional) Migrate your existing database

Export the existing database from the old host with the following command. (You
can find the proper values for <username> and <databasename> in the
`config.inc.php` file in the Archon root directory.)

    mysqldump -u <username> -p <databasename> > archon.sql

On the new host:

    $ mysql -u <username> -p <databasename>

At the prompt, type the following to load the database:

    mysql> USE <databasename>;
    mysql> source archon.sql



## Test the installation

Try to load the Archon web interface.

If you get something like "Error connecting to database!":

- Make sure the database adapter is set to `MySQLi` in `config.inc.php`
- Make sure you installed the PEAR dependencies listed aboved

If you get a mostly blank page, it might mean that your old Archon site was
using a custom theme which needs to be copied over to the new box. See the next
section for more info. For quick testing, just change the theme to the default
using the admin interface:

    http://your-site.your-domain.com/admin

Log in as the `sa` user and go to Archon Administration | Archon Configuration
| Default Theme. Then pick the theme called "default".


## (Optional) Copy over any customizations from old Archon install

If your Archon installation is customized, you'll need to make those same
customizations to the new codebase.
