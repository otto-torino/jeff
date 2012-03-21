JEFF PHP FRAMEWORK by Otto srl, MIT license             {#mainpage}
============================================

Jeff is a light php framework design to help the programmer in the 
developement of a web site or web application. It's written
following the MVC pattern. Other patterns are used in the
framework.   

Please visit the [project site](http://jeff.otto.to.it) for more details.   

Jeff modules and plugins are available in the [otto-torino github repository](http://www.github.com/otto-torino).

REQUIREMENTS
------------
- php >= 5.3   
- mysql >= 5 (may be easily extended to use other DBMS)   
- web server (apache >= 2.0, nginx, ...)   

the following web server modules have to be enabled (the modules' names are the apache ones)
- mod_expires   
- mod_headers   
- mod_rewrite   

the **short_open_tag** directive must be enabled in php.ini   

INSTALL
--------

- copy all files into a directory under the web server root.   
- Adjust the RewriteBase Rule in **ROOT/.htaccess** and **ROOT/admin/.htaccess** to   
fit your path situation.
- create the db using the **jeff_[lng].sql** file (english and italian version availables)
- configure the db connection parameters in **configuration.php**
- surf your install directory et voilà
