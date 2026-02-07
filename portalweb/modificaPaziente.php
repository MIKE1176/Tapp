<?php
include('./session.php');

// Recupero i dati dal form gestionePazienti
$id = $_POST['idPaziente'];
$cf = strtoupper($_POST['cf']);
$nome = firstUpperSenteces('nome');        //mette la prima lettera maiuscola, il resto minuscolo
$cognome = firstUpperSenteces('cognome');
$dataNascita = $_POST['dataNascita'];
$luogoNascita = mysqli_real_escape_string($db,strtoupper($_POST['luogoNascita']));
$sesso = $_POST['sesso'];
$indirizzo = firstUpperSenteces('indirizzo');
$civico = $_POST['civico'];
$citta = strtoupper($_POST['citta']);
$socio = $_POST['socio'];

if($socio == "True"){
  $socio = 1;
}elseif($socio == "False"){
  $socio = 0;
}

$telefono = $_POST['telefono'];
$notePaziente = mysqli_real_escape_string($db,$_POST['notePaziente']);    //funzione che permette di inserire caratteri speciali per non far andare in errore sql

if(mysqli_fetch_assoc(mysqli_query($db,"SELECT paziente.attivo FROM paziente WHERE id = '$id';"))['attivo'] == 1){
    $attivo = 1;
}else{
    $attivo = 0;
}



$queryCF = mysqli_query($db, "SELECT * FROM paziente WHERE paziente.CF = '$cf'");
$myCF = mysqli_query($db, "SELECT * FROM paziente WHERE paziente.id = '$id'");

if (mysqli_num_rows($queryCF) > 0 and $cf!=mysqli_fetch_assoc($myCF)['CF']){
  $_SESSION['errore'] = "mPazienti";
  header("location: ./gestionePazienti.php");
  mysqli_close($db);
}else{
  $query = "UPDATE paziente SET CF='$cf', nome = '$nome', cognome = '$cognome', dataNascita = '$dataNascita', luogoNascita = '$luogoNascita', sesso = '$sesso', indirizzo = '$indirizzo', civico = '$civico', citta = '$citta', socio = '$socio', telefono = '$telefono', notePaziente = '$notePaziente', attivo = '$attivo' WHERE ID = '$id';";
  $paziente = mysqli_query($db, $query);
  header("location: ./gestionePazienti.php");
  mysqli_close($db);
}
