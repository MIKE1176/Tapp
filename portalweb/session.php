<?php
   include('./config.php');
   include('../utils.php');

   $portalName = "portalweb"; 

   if (session_status() === PHP_SESSION_NONE) {
      session_name($portalName . "_SESSION");
      session_start();
   }

   function check_auth($is_login_page = false) {
      global $portalName,$db;
      
      // Controllo se le chiavi esistono e corrispondono (Coerenza con il tuo login)
      $is_logged = isset($_SESSION['ID']) && 
                  isset($_SESSION['session']) && 
                  $_SESSION['session'] === $portalName;

      if ($is_login_page) {
         if ($is_logged) {
               header("Location: index.php");
               exit;
         }
      } else {
         if (!$is_logged) {
               header("Location: login.php"); // o accedi.php, decidi il nome
               exit;
         }
        
         // --- REFRESH DATI UTENTE (Solo se già loggato e non è la pagina di login) ---

         $stmt = $db->prepare("SELECT ID, nome, username, utente FROM operatore WHERE username = ? AND attivo = 1");
         $stmt->bind_param("s", $_SESSION['username']);
         $stmt->execute();
         $result = $stmt->get_result();

         if ($user_check = $result->fetch_assoc()) {
               $_SESSION['ID'] = $user_check['ID'];
               $_SESSION['nome'] = $user_check['nome'];
               $_SESSION['username'] = $user_check['username'];
               $_SESSION['auth'] = $user_check['utente'];
         } else {
               // Se l'utente non è più attivo nel DB, killiamo la sessione
               header("Location: logout.php"); 
               exit;
         }
         $stmt->close();
      } 
   }
?>