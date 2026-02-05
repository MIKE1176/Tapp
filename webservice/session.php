<?php
   include('./config.php');
   include('../utils.php');
   
   // --- CONFIGURAZIONE DEL PORTALE ---

   $portalName = "webservice"; 

   // --- SICUREZZA E AVVIO ---
   if (session_status() === PHP_SESSION_NONE) {
      session_name($portalName . "_SESSION");
      session_start();
   }

   /**
    * Funzione per controllare se l'utente è loggato.
    * Risolve il problema dell' "Undefined array key".
    */
   function check_auth($is_login_page = false) {
      global $portalName,$db;
      
      // Verifichiamo se le chiavi esistono (evita i Warning)
      $is_logged = isset($_SESSION['ID']) && 
                  isset($_SESSION['session']) && 
                  $_SESSION['session'] === $portalName;

      if ($is_login_page) {
         // Se siamo nella pagina di login e l'utente è GIÀ loggato -> vai alla home
         if ($is_logged) {
               header("Location: home.php");
               exit;
         }
      } else {
         // Se siamo in una pagina protetta e NON è loggato -> vai al login
         if (!$is_logged) {
               header("Location: accedi.php");
               exit;
         }

         // --- REFRESH DATI UTENTE (Solo se già loggato e non è la pagina di login) ---

         $stmt = $db->prepare("SELECT nome, cognome, username, ID FROM utente WHERE username = ?");
         $stmt->bind_param("s", $_SESSION['username']);
         $stmt->execute();
         $result = $stmt->get_result();

         if ($user_check = $result->fetch_assoc()) {
               $_SESSION['ID'] = $user_check['ID'];
               $_SESSION['nome'] = $user_check['nome'];
               $_SESSION['cognome'] = $user_check['cognome'];
         } else {
               // Se l'utente non è più attivo nel DB, killiamo la sessione
               header("Location: logout.php"); 
               exit;
         }
         $stmt->close();

      }
   }
?>
