JEFF PHP FRAMEWORK by Otto srl, MIT license
===================================================================

Version 0.98

Jeff is a light php framework design to help the programmer in the 
developement of a web site or web application. It's written
following the MVC pattern. Other patterns are used in the
framework.   

Please visit the project page for more details:   
http://www.abidibo.net/projects/php/jeff

Jeff modules are available as github repository, please visit    
http://www.github.com/otto-torino

REQUIREMENTS
------------
- php >= 5   
- mysql >= 5 (may be easily extended to use other DBMS)   
- apache >= 2   

the following apache modules have to be enabled   
- mod_expires   
- mod_headers   
- mod_rewrite   

the short_open_tag directive must be enabled in php.ini   

set your DBMS in order to work with utf8 text encoding  
 
INSTALL
--------

* copy all files into a directory under the web server root.   
* Adjust the RewriteBase Rule in **.htaccess** and **admin/.htaccess** to   
fit your path situation.
* create the db using the **jeff_[lng].sql** file (english and italian version availables)
* configure the db connection parameters in **configuration.php**
* surf your install directory and oila
