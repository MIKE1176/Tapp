<?php
include('./session.php');

// Recupero i dati dal form gestioneVolontari
$nome = firstUpperSenteces('nome');
$cognome = firstUpperSenteces('cognome');
$dataNascita = $_POST['dataNascita'];
$sesso = $_POST['sesso'];
$telefono = $_POST['telefono'];
$username = $_POST['username'];
$password = password_hash($username, PASSWORD_DEFAULT);
$responsabile = $_POST['responsabile'];
$attivo = 1;

if (mysqli_num_rows(mysqli_query($db, "SELECT * FROM volontario WHERE volontario.username = '$username'")) > 0){
  $_SESSION['errore'] = "username";
  header("location: gestioneVolontari.php");
  mysqli_close($db);
}else{

  $query = "insert into volontario(nome, cognome, dataNascita, sesso, telefono, username, password, responsabile, attivo) values('$nome', '$cognome', '$dataNascita', '$sesso', $telefono, '$username', '$password', '$responsabile','$attivo');";
  $volontario = mysqli_query($db, $query);
  header("location: gestioneVolontari.php");
  mysqli_close($db);
}
