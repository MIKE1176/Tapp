<?php
   $mode = "windows";

   if ($mode == "linux") {
      //linux
      define('DB_SERVER', 'web-dev-mysql-db-1');
      define('DB_USERNAME', 'root');
      define('DB_PASSWORD', 'root');
      define('DB_DATABASE', 'tappDB');
      $db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
   } else if ($mode == "windows") {
      //windows
      define('DB_SERVER', 'localhost:3306');
      define('DB_USERNAME', 'root');
      define('DB_PASSWORD', '');
      define('DB_DATABASE', 'tappDB');
      $db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
   } else {
      /*case default
         UTENTI:
         - http://localhost/tapp-def/tapp/webservice/index.php
         PORTALE WEB:
         - http://localhost/tapp-def/tapp/portalweb/index.php
      */
   }