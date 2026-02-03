<?php
   include('config.php');
   session_name("webservice");
   session_start();

   if(!isset($_SESSION['id']) || !isset($_SESSION['sessione']) || $_SESSION['sessione'] !== 'webservice'){ // Verifica se l'utente ha già effettuato l'accesso, se sì, reindirizza alla pagina di benvenuto
      header('Location: accedi.php');
      exit;
   }