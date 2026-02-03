<?php
   include('config.php');
   include('utils.php');
   session_name("portalweb");
   session_start();
   
   if(!isset($_SESSION['username']) || !isset($_SESSION['sessione']) || $_SESSION['sessione'] !== 'portalweb'){
      header("location:login.php");
      exit;
   }

   $session_user = $_SESSION['username'];  
   $user_check = mysqli_query($db,"select username, nome, utente, ID from operatore where username = '$session_user' ");

   if(mysqli_num_rows($user_check) != 1){
      header("location:login.php");
   }
   
   $user_check = mysqli_fetch_assoc($user_check);
   
   $_SESSION['nome_operatore'] = $user_check['nome'];
   $_SESSION['utente'] = $user_check['utente'];
   $_SESSION['ID_operatore'] = $user_check['ID'];
   $login_session = $user_check['username'];