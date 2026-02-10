<?php
  include("session.php");
  check_auth();

  if($_SESSION['auth']!="AMMINISTRATIVO"){
    header("location: index.php");
    exit();
  }

  // Recupero i dati dal form gestioneOperatori
  $nome = mysqli_real_escape_string($db, firstUpperSentences($_POST['nome']));
  $cognome = mysqli_real_escape_string($db, firstUpperSentences($_POST['cognome']));
  $dataNascita = mysqli_real_escape_string($db, $_POST['dataNascita']);
  $sesso = mysqli_real_escape_string($db, $_POST['sesso']);
  $telefono = mysqli_real_escape_string($db, $_POST['telefono']);
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = password_hash($username, PASSWORD_DEFAULT);
  $responsabile = mysqli_real_escape_string($db, $_POST['responsabile']);
  $attivo = 1;

  
  if (mysqli_num_rows(mysqli_query($db, "SELECT * FROM operatore WHERE operatore.username = '$username'")) > 0){
    $_SESSION['errore'] = "username";
  }else{

    $query = "INSERT INTO operatore(nome, cognome, dataNascita, sesso, telefono, username, password, utente, attivo) 
              VALUES ('$nome', '$cognome', '$dataNascita', '$sesso', $telefono, '$username', '$password', '$responsabile','$attivo');";
    
    mysqli_query($db, $query);
  }
  mysqli_close($db);
  header("location: gestioneOperatori.php");
  exit();
?>