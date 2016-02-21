## Setting up provisioning for a new project

### Overview

This document serves as a quick-start guide to setting up vagrant for a mediamechanic project. In general, this process consists of creating a few directories and a few files. The files are listed at the top of this document, instructions for provisioning are next,  and a detailed description of each provisioning file follows.
    
### Provisioning Files
1. \${REPO}/provision/vagrant.vhost
2. \${REPO}/provision/provision.sh
3. \${REPO}/sql/localhost.sql
4. \${REPO}/sql/updates/*.sql 

### Getting a provisioned project

1. Check out the git repository for the project from bitbucket.
2. cd into the project root ( e.g.: `cd web.indow` )
3. run the command `vagrant up`
4. hit the url in your browser ( `http://127.0.0.1:8080` )

### Connecting to MySQL with a local client (note: had some issues with multiple vagrant sesions, so ports have changed...)

1. ssh host: 127.0.0.1
2. ssh port: 2231
3. ssh username: vagrant
4. ssh password: vagrant
5. mysql hostname: 127.0.0.1
6. mysql server port: 3306
7. mysql username: root
8. mysql password: root
9. mysql default schema: indowwin_db

### Provisioning File Directory Structure
In your project root directory, add a directory tree which includes the following (soon there will be a baseline repository for forking purposes):

    * provision
        --- provision.sh
        --- vagrant.vhost
    
    * sql
        --- localhost.sql
        * updates
        --- any updated .sql files
        


### vagrant.vhost
 
This file is a simple vhosts entry which will be copied to the httpd.conf file during the provisioning process. An example file follows:

    NameVirtualHost *:80
    
    <VirtualHost *:80>
        DocumentRoot "/var/www/public_html"
        ServerName indow.com
        <Directory "/var/www/public_html">
            Options Indexes FollowSymLinks MultiViews
            AllowOverride All
            Order allow,deny
            Allow from all
        </Directory>
    </VirtualHost>
 
### provision.sh
 
 The provision.sh file contains the script which loads all software packages once the box is created, and populates the database. An example file follows:
 
    #!/usr/bin/env bash

    if [ ! -f "/var/indow_provision" ];
    then
    
        echo "* -- Provisioning LAMP..."
        echo "-- 1/7 -- Updating apt..."
        apt-get update #>/dev/null 2>&1
    
    
        echo "-- 2/7 -- Installing Apache..."
        apt-get install -q -y apache2
        rm -rf /var/www
        ln -fs /vagrant /var/www
    
    
        echo "-- 3/7 -- Installing MySQL..."
        export DEBIAN_FRONTEND=noninteractive
        apt-get purge -q -y mysql-common
        apt-get install -q -y mysql-server
        apt-get install -q -y mysql-client
        service mysql restart
    
    
        echo "-- 4/7 -- Installing PHP..."
        apt-get install -q -y php5 libapache2-mod-php5 php5-mysql php5-curl php5-xsl php5-cli
        a2enmod rewrite
    
    
        echo "-- 6/7 -- Creating Database..."
        mysql -uroot < /vagrant/sql/localhost.sql
    
        for i in $(ls -d /vagrant/sql/updates/*)
        do  
            mysql -uroot < $i
        done
    
    
        echo "-- 7/7 -- Starting Apache..."
        usermod -a -G vagrant www-data
        cat /vagrant/provision/vagrant.vhost > /etc/apache2/httpd.conf
        service apache2 restart
    
    else
    
        echo "-- 1/1 -- Updating Database..."
        for i in $(ls -d /vagrant/sql/updates/*)
        do  
            if [ $i -nt /var/indow_provision ]
            then
                mysql -uroot < $i
            fi  
        done
    
    fi
    
    touch /var/indow_provision
    
    echo "-- Done."
    
### localhost.sql
 
The localhost.sql file located in the /sql directory is responsible for initial database population. This is the file which will contain your initial database structure. Modifications to this structure will be placed into the updates directory.

###Managing database structural changes

The new system for managing database changes has been implemented. The following contains details on how to work with it. The primary goal of this system is to increase testabilty and maintainabilty of our database versioning.

Here's an [example commit](https://bitbucket.org/mediamechanic/web.indow/commits/3fb3fb2ba5f475cbaf9a72d2b28d33aaeba069d4) that shows the minimum requirements for making a change to the database.

We also recommended that you read the [documentation](https://ellislab.com/codeigniter/user-guide/libraries/migration.html), and this [tutorial](http://zacharyflower.com/2013/08/12/getting-started-with-codeigniter-migrations/) to get a basic understanding of CodeIgniter's system. The main difference between our implementation and CodeIgniter's, is that we forgo the use of DBForge, since it's support of foreign keys is incredibly weak. Instead, we use mysql queries directly.

1. Increment the `migration_version` in `config/migration.php`
2. Create a new Migration Subclass in the migrations folder. The filename must have the zero-padded `migration_version`, followed by a simple description of the changes. The Class name must match the file name, replacing the migration_version with 'Migration'
3. Override the up() function, wrapping your db changes in a transaction
4. **DO NOT COMMIT/PUSH YOUR CHANGES UNTIL YOU'VE VERIFIED THAT YOU CAN RUN `vagrant provision` WITHOUT ERROR**