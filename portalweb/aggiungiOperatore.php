<?php
  include("session.php");
  check_auth(); // Se non loggato o non attivo, scappa e va al login

  if($_SESSION['auth']!="AMMINISTRATIVO"){
    header("location: index.php");
  }

  // Recupero i dati dal form gestioneOperatori
  $nome = firstUpperSentences($_POST['nome']);
  $cognome = firstUpperSentences($_POST['cognome']);
  $dataNascita = $_POST['dataNascita'];
  $sesso = $_POST['sesso'];
  $telefono = $_POST['telefono'];
  $username = $_POST['username'];
  $password = password_hash($username, PASSWORD_DEFAULT);
  $responsabile = $_POST['responsabile'];
  $attivo = 1;

  if (mysqli_num_rows(mysqli_query($db, "SELECT * FROM operatore WHERE operatore.username = '$username'")) > 0){
    $_SESSION['errore'] = "username";
    header("location: gestioneOperatori.php");
    mysqli_close($db);
  }else{

    $query = "insert into operatore(nome, cognome, dataNascita, sesso, telefono, username, password, utente, attivo) values('$nome', '$cognome', '$dataNascita', '$sesso', $telefono, '$username', '$password', '$responsabile','$attivo');";
    $operatore = mysqli_query($db, $query);
    header("location: gestioneOperatori.php");
    mysqli_close($db);
  }
?>