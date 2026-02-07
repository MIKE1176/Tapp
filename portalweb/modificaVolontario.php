<?php
include('./session.php');


// Recupero i dati
$id = $_POST['idVolontario'];
$nome = firstUpperSenteces('nome');
$cognome = firstUpperSenteces('cognome');
$dataNascita = $_POST['dataNascita'];
$sesso = $_POST['sesso'];
$telefono = $_POST['telefono'];
$username = $_POST['username'];
$password = password_hash($username, PASSWORD_DEFAULT);
$responsabile = $_POST['responsabile'];
if(mysqli_fetch_assoc(mysqli_query($db,"SELECT volontario.attivo FROM volontario WHERE id = '$id';"))['attivo'] == 1){
  $attivo = 1;
}else{
  $attivo = 0;
}


$queryDenominazione = mysqli_query($db, "SELECT * FROM volontario WHERE volontario.username = '$username'");
$myDenominazione = mysqli_query($db, "SELECT * FROM volontario WHERE volontario.id = '$id'");

if (mysqli_num_rows($queryDenominazione) > 0 and $username!=mysqli_fetch_assoc($myDenominazione)['username']){ //controllo che non ci siano altri volontari con lo stesso username come quella, unica eccezzione se stesso
  $_SESSION['errore'] = "mVolontario";
  header("location: gestioneVolontari.php");
  mysqli_close($db);
}else{

  $query = "UPDATE volontario SET nome = '$nome', cognome = '$cognome', dataNascita = '$dataNascita', sesso = '$sesso', telefono = '$telefono', username = '$username', password = '$password', responsabile = '$responsabile', attivo = $attivo WHERE id = $id;";
  $volontari = mysqli_query($db, $query);
  header("location: gestioneVolontari.php");
  mysqli_close($db);
}
