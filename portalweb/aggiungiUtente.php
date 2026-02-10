<?php
  include("session.php");
  check_auth();

  if($_SESSION['auth']!="AMMINISTRATIVO"){
    header("location: index.php");
    exit();
  }

  // Recupero i dati dal form gestioneUtenti
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
  $attivo = 1;

  // Controllo duplicati
  if (mysqli_num_rows(mysqli_query($db, "SELECT * FROM utente WHERE CF = '$CF'")) > 0){
      $_SESSION['errore'] = "cfUtente";
  } else {
      $query = "INSERT INTO utente (CF, nome, cognome, dataNascita, luogoNascita, sesso, indirizzo, civico, citta, telefono, noteUtente, attivo) 
                VALUES ('$CF', '$nome', '$cognome', '$dataNascita', '$luogoNascita', '$sesso', '$indirizzo', '$civico', '$citta', '$telefono', '$noteUtente', $attivo)";
      
      mysqli_query($db, $query);
  }
  header("location: ./gestioneUtenti.php");
  mysqli_close($db);
  exit();

?>