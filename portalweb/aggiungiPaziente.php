<?php
include('./session.php');

// Recupero i dati dal form gestionePazienti
$CF = strtoupper($_POST['cf']);
$nome = firstUpperSenteces('nome');        //mette la prima lettera maiuscola, il resto minuscolo
$cognome = firstUpperSenteces('cognome');
$dataNascita = $_POST['dataNascita'];
$luogoNascita = firstUpperSenteces('luogoNascita');
$sesso = $_POST['sesso'];
$indirizzo = firstUpperSenteces('indirizzo');
$civico = $_POST['civico'];
$citta = strtoupper($_POST['citta']);
$socio = $_POST['socio'];
$attivo = 1;

if($socio == "True"){
  $socio = 1;
}elseif($socio == "False"){
  $socio = 0;
}

$telefono = $_POST['telefono'];
$notePaziente = mysqli_real_escape_string($db,$_POST['notePaziente']);    //funzione che permette di inserire caratteri speciali per non far andare in errore sql

if (mysqli_num_rows(mysqli_query($db, "SELECT * FROM paziente WHERE paziente.CF = '$CF'")) > 0){
  $_SESSION['errore'] = "CF";
  header("location: ./gestionePazienti.php");
  mysqli_close($db);
}else{
  $query = "insert into paziente(CF,nome,cognome,dataNascita,luogoNascita,sesso,indirizzo,civico,citta,socio,telefono,notePaziente,attivo) values('$CF', '$nome', '$cognome', '$dataNascita', '$luogoNascita', '$sesso', '$indirizzo', '$civico', '$citta','$socio', '$telefono', '$notePaziente',$attivo);";
  $paziente = mysqli_query($db, $query);
  header("location: ./gestionePazienti.php");
  mysqli_close($db);
}
