<?php
  include("session.php");
  check_auth();

  if($_SESSION['auth']!="AMMINISTRATIVO"){
    header("location: index.php");
    exit();
  }

  $id = mysqli_real_escape_string($db, $_POST['idUtente']);
  $CF = mysqli_real_escape_string($db, strtoupper($_POST['cf']));
  $nome = mysqli_real_escape_string($db, firstUpperSentences($_POST['nome']));
  $cognome = mysqli_real_escape_string($db, firstUpperSentences($_POST['cognome']));
  $dataNascita = mysqli_real_escape_string($db, $_POST['dataNascita']);
  $luogoNascita = mysqli_real_escape_string($db, firstUpperSentences($_POST['luogoNascita']));
  $sesso = mysqli_real_escape_string($db, $_POST['sesso']);
  $indirizzo = mysqli_real_escape_string($db, firstUpperSentences($_POST['indirizzo']));
  $civico = mysqli_real_escape_string($db, $_POST['civico']);
  $citta = mysqli_real_escape_string($db, strtoupper($_POST['citta']));
  $telefono = mysqli_real_escape_string($db, $_POST['telefono']);
  $noteUtente = mysqli_real_escape_string($db, $_POST['noteUtente']);

  // Controllo se l'utente esiste e il suo stato attuale
  $checkUtente = mysqli_query($db, "SELECT * FROM utente WHERE id = '$id'");
  $datiAttuali = mysqli_fetch_assoc($checkUtente);
  $attivo = $datiAttuali['attivo'];

  // Controllo se il nuovo CF è già usato da ALTRI utenti
  $queryCF = mysqli_query($db, "SELECT * FROM utente WHERE CF = '$CF' AND id != '$id'");

  if (mysqli_num_rows($queryCF) > 0){
    $_SESSION['errore'] = "mUtenti";
    header("location: ./gestioneUtenti.php");
  } else {
    $query = "UPDATE utente SET 
              CF='$CF', 
              nome = '$nome', 
              cognome = '$cognome', 
              dataNascita = '$dataNascita', 
              luogoNascita = '$luogoNascita', 
              sesso = '$sesso', 
              indirizzo = '$indirizzo', 
              civico = '$civico', 
              citta = '$citta', 
              telefono = '$telefono', 
              noteUtente = '$noteUtente', 
              attivo = '$attivo' 
              WHERE id = '$id'";
    
    mysqli_query($db, $query);
    header("location: ./gestioneUtenti.php");
  }
  mysqli_close($db);
  exit();
?>