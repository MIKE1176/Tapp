<?php
   include('./session.php');
   
   if(session_destroy()) {  // a differenza di session_unset che elimina solo le variabili, distrugge tutta la sessione
      header("location: login.php");
      exit;
   }