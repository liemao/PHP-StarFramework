# PHP-StarFramework
Star Framework是一个简单、高效、实用的PHP框架。
#Requirement
PHP5.2+
#Layout

A classic Application directory layout:
```
+ public
  | - .htaccess // Rewrite rules
  | - index.php // Application entry
  | + static
      | + css
      | + js
      | + img
- application/
  - Bootstrap.php   // Bootstrap
  + configs
      | - application.ini // Configure 
  + controllers
     - IndexController.php // Default controller
  + layouts
     | + default
         - layout.phtml // layout
  + logs //Log
  + models //Model
  + services //Service
  + views    
      | + scripts
          |+ index   
            - index.phtml // View template for default controller
+ library
    | + Star //Star Framework
```
#DocumentRoot

you should set DocumentRoot to application/public, thus only the public folder can be accessed by user

#index.php
```php
<?php
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require 'Star/Application.php';

// Create application, bootstrap, and run
$application = new Star_Application(
    APPLICATION_ENV,
    APPLICATION_PATH,
    APPLICATION_PATH . '/configs/application.ini', //OR  APPLICATION_PATH . '/configs/application.php',
    realpath(APPLICATION_PATH . '/../library')
);
$application->bootstrap()->run();
```

#Rewrite rules
Apache
```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} -s [OR]
RewriteCond %{REQUEST_FILENAME} -l [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^.*$ - [NC,L]
RewriteRule !\.(js|css|png|jpg|jpeg|gif|swf|ico|html|htm)$ index.php [NC,L]
```

Nginx
```
server {
  listen ****;
  server_name  domain.com;
  root   document_root;
  index  index.php index.html index.htm;

  location ~* \.(js|css|png|jpg|jpeg|gif|swf|ico|html|htm)$ {
      break;
  }

  if (!-e $request_filename) {
    rewrite ^/(.*)  /index.php/$1 last;
  }
}
```

#application.ini
application.ini is the application config file
```
[production]
phpSettings.display_startup_errors = 0
phpSettings.display_errors = 0
;includePaths.library = APPLICATION_PATH "/../library"
bootstrap.path = APPLICATION_PATH "/Bootstrap.php"
bootstrap.class = "Bootstrap"
```

alternatively, you can use a PHP array instead: 
#application.php
```php
<?php
return array(
    'production' => array(
        'bootstrap' => array(
            'path' => APPLICATION_PATH . "/Bootstrap.php",
            'class' => 'Bootstrap',
        ),
    ),
);
```
#default controller
In StarFramework, the default controller is named IndexController:
```php
<?php
class IndexController extends Star_Controller_Action
{
    public function init()
    {
	
    }

    public function indexAction()
    {
        $this->view->assign(array(
            'content' => 'Hello world.',
        ));
        $this->view->title = 'Hello world';
    }
}
?>
```

#view script
The view script for default controller and default action is in the application/views/scripts/index/index.phtml, Yaf provides a simple view engineer called "Star_View", which supported the view template written by PHP.
```php
<html>
 <head>
   <title><?php echo $this->title;?></title>
 </head>
 <body>
   <?php echo $this->content; ?>
 </body>
</html>
```

