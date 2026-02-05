<?php
   include("./session.php");

   // 1. Svuota completamente l'array delle variabili di sessione
   $_SESSION = array();

   // 2. Cancella il cookie di sessione dal browser
   // Questo è fondamentale quando hai più portali per evitare conflitti
   if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(
         session_name(),      // Il nome specifico (es. PORTALE_A_SESSION)
         '',                  // Valore vuoto
         time() - 42000,      // Scadenza nel passato per eliminarlo
         $params["path"],     // Il percorso specifico che abbiamo impostato
         $params["domain"], 
         $params["secure"], 
         $params["httponly"]
      );
   }

   // 3. Distruggi la sessione sul server
   session_destroy();
   
   if (session_status() !== PHP_SESSION_NONE) {
       session_write_close();
   }

   // 4. Redirect e stop dello script
   header("Location: accedi.php");
   exit;
?>