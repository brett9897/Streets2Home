Streets2Home
============
Version 2.0
------------

This was an attempt to clean up a project that came from another group.  It just was not salvageable.  The rewrite of this is at [a s2h_cake](https://github.com/brett9897/s2h_cake)

Steps to get this running on your local machine

#### 1. Clone the repository 

> $ git clone https://github.com/brett9897/Streets2Home.git [directory name]

#### 2. Copy dbconfig.php.default to dbconfig.php

#### 3. Update dbconfig.php to match your connection settings

#### 4. create 'survey_offline_static.html'

> $ touch survey_offline_static.html  

#### 5. Make sure they both have other write privileges
This shouldn't be a problem if you are using Windows
> $ chmod o+w survey_offline_static.html  

#### 6. Use the sql file in sql_s2h to create your database and populate it with test data
The amount of data might be too much if you are using PHPMyAdmin, so you might have to do it in mysql command line.
Just change to the directory that the sql file is in and then start mysql.

> $ cd sql_s2h  
> $ mysql -u \<username\> -p \<database_name\>  
> Enter password:  
> mysql\> source StreetsToHome2_Database_2013-01-07_2230.sql

After that it should all work
