CHANGELOG
===========================================================================================

2013-05-15, abidibo  <abidibo@gmail.com>
---------------------------------------------    
Preparing new version: v1.0

Bug fixed in adminTable exportation functionality

Added translations in export functionality

Added a mobile framework:    
- mobile detection
- mobile site activation through site settings
- mobile condition implemented in the core class, change condition to fit your needs, default returns true is mobile_site setting is true and a mobile client is detected
- site template <template_name>_mobile.tpl is loaded (if exist) when the mobile site is requested (searched in the current theme directory, fallback to default theme occurs only if no-mobile theme also fallbacks)
- current theme css 'stylesheet_mobile.css' is added (if exists) after the no-mobile 'stylesheet.css'
- site templates specific css <template_name>_mobile.css are added (if exist) after no-mobile css when the mobile site is requested, 
  (searched in the current theme directory, fallback to default theme occurs only if no-mobile css also fallbacks)
- modules templates <tpl_name>_mobile.php are loaded (if exist) when the mobile site is requested, (searched in the current theme view directory, fallback to default theme occurs only if no-mobile tpl also fallbacks)
- modules' specific css with '_mobile' suffix are added (if exist) when the mobile site is requested, (searched in the current theme css directory, fallback to default theme occurs only if no-mobile css also fallbacks)

2013-05-14, abidibo  <abidibo@gmail.com>
---------------------------------------------    
Preparing new version: v1.0

Updated dijit editor version to 1.9.0

Raise 404 when calling a not callable module's method

Implementation of 403 error

Caching of getTableStructure query to avoid mysql bad performance

2013-02-15, abidibo  <abidibo@gmail.com>
---------------------------------------------    
Preparing new version: v1.0

Added toggle checkbox images in adminTable

2013-02-07, abidibo  <abidibo@gmail.com>
---------------------------------------------    
Preparing new version: v1.0

Added possibility for super admin to change its account properties (pwd)

2013-02-07, abidibo  <abidibo@gmail.com>
---------------------------------------------    
Preparing new version: v1.0

Added fields labels support in adminTable, with label and helptext

Added custom text option before adminTable form (edit and insert)

2013-01-28, abidibo  <abidibo@gmail.com>
---------------------------------------------    
Preparing new version: v1.0

Added is_foreign property to adminTable class, allows to check foreign keys before record deletion

Added __toString method to models

View method in adminTable class splitted (easier to subclass)

Added fieldsets support in auto generated backoffice

Added multicheck support to filter fields in adminTable

Added some special fields (constant, date, datetime)

Added download method to the router class

Added sanitize function to php functions lib

Some minor changes and fixes

Added user active field

2012-07-12, abidibo  <abidibo@gmail.com>
---------------------------------------------    
Preparing new version: v1.0

Added raise404 method to the error class to manage 404 errors (inexistent objects, pages, files...)

2012-03-21, abidibo  <abidibo@gmail.com>
---------------------------------------------    
Preparing new version: v1.0

Moved charge editor js from function.php to form.js
Splitted the function.php chargeEditor function into prepareJsEditor and chargeEditor

Both changes allow to charge the editor dinamycally for example through a callback of an ajaxRequest

Added save_and_continue option to adminTable class to show or not the save and continue button (useless with ajax)

2012-03-21, abidibo  <abidibo@gmail.com>
---------------------------------------------    
Preparing new version: v1.0

Added 'no_form_fields' option to adminTable class to avoid some fields manipulation through form (i.e insertion_date, last_edit_date, ...)
Added loadController method to router class
Added model_name parameter to adminTable::saveField in order to use it instead of the general model class if given (custom saveData function)

2012-03-21, abidibo  <abidibo@gmail.com>
---------------------------------------------    
New version: v0.99
 
Full code documentation, menu and singleton pattern application and some minor adjustments

### New features:

- **share function**: new function added in order to ease the creation of share links. The supported social platforms are facebook, twitter, linkedin, googleplus and digg.
- **cut html text function**: the new function allows to truncate an html preserving the correct tag closure
- **full text search**: added a new core class which allows the execution of full text searches with results highlighting (for MySQL only). The use of this new class requires a database custom function which is provided in the file itself and must be added to your MySQL instance
- **new db client methods** added in order to allow the direct execution of select statements
- **singleton**: singleton superclass added in the core of jeff, implementing the singleton pattern. The registry and db factory classes inherits from it assuring the existence of only one registry and db client instance. All classes were updated in order to use the singleton instance, since now it's no longer necessary to pass the registry object everywhere
- **mysql db client** class moved from modules to the core
- **mime types** check improvements in the form class and adminTable class when uploading files
- **menu module** main menu voices now stored on the database
- **source code documentation**

### Bug fixed

- click event bug in datepicker javascript library in the navigation through months and years

___________________________________________________________________________________________

2012-03-21, abidibo  <abidibo@gmail.com>
---------------------------------------------    
Preparing version: v0.99
 
Full code documentation, menu and singleton pattern application and some minor adjustments

### Changes:

- all php files, some have been removed, some added, some renamed
- menu module
- README

___________________________________________________________________________________________  
    
2012-03-09, abidibo  <abidibo@gmail.com>
---------------------------------------------    

Preparing version: v0.99
 
mime-type improvements in form and adminTable classes and some minor changes

### Changes:

- core/form.class.php (added mime-types)
- core/adminTable.class
  * split of image and file preview in admin table list
  * added 'check_content' and 'contents_allowed' options for file special fields
  * added class attribute to insert record link
- core/core.php (added url property containing server request uri)
- themes/default/css/stylesheet.css (added submit class)
- themes/white/css/stylesheet.css (same as above)
- modules/group/group.controller.php
  * added class attribute to insert record link
- jeff_en.sql, jeff_it.sql (removed user defined groups)

___________________________________________________________________________________________  

2012-03-05, abidibo  <abidibo@gmail.com>
---------------------------------------------    

Preparing version: v0.99

Bug fixed in datepicjer js library

### Changes:

- lib/js/datepicker.js

___________________________________________________________________________________________  

2012-02-21, abidibo  <abidibo@gmail.com>
---------------------------------------------    

Preparing version: v0.99

Singleton rulez. Registry and Db classes inherits from singleton, so the existence of only one instance is granted.

### Changes:

- added core/singleton.class.php
- class core/registry.class.php now extends singleton (not needed anymore to pass registry everywhere)
- class core/db/db.factory.php now extends singleton (added instance method and removed getInstance)
- include.php
- removed mysql folder inside modules and added mysql.php in core/db
- core/core.php
  * added singleton calls
  * removed $_SESSION['theme'] used for translations, now __() function can get its registry singleton instance, which contains the theme property

___________________________________________________________________________________________  

2012-02-16, abidibo  <abidibo@gmail.com>
---------------------------------------------    

Preparing version: v0.99

Added a full text search class

### Changes:

- added core/search.class.php (notice: query to execute in order to use replace_ci function)
- edited core/include.php
- edited modules/mysql/mysql.php
  * added queryResult method
  * edited autoSelect method
- edited core/db/interface.db.php
- added cutHtmlText function to lib/php/functions.php

___________________________________________________________________________________________  

2012-01-26, abidibo  <abidibo@gmail.com>
---------------------------------------------    

Preparing version: v0.99

Added share function (social networks)
	
### Changes:

- added share function to lib/php/functions.php.

___________________________________________________________________________________________  

2012-01-26, abidibo  <abidibo@gmail.com>
---------------------------------------------    

New version: v0.98

A dive into html5

### New features:

- added modernizr (http://www.modernizr.com) js library and removed html5.js one.
- added label form attribute
- mandatory fields styled bold (the star was removed)
- added email field support in the form class (pattern is automatically set)
- updated doctype, root element and meta character accordingly to html5 specifications
- added the possibility to change/add meta tags and link tags inside head at runtime (social, feed, ...)
- creation of this changelog
