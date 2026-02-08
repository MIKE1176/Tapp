<?php
  include("session.php");
  check_auth();

  if($_SESSION['auth']!="AMMINISTRATIVO"){
    header("location: index.php");
  }

  // Recupero i dati
  $id = $_POST['idOperatore'];
  $nome = firstUpperSentences($_POST['nome']);
  $cognome = firstUpperSentences($_POST['cognome']);
  $dataNascita = $_POST['dataNascita'];
  $sesso = $_POST['sesso'];
  $telefono = $_POST['telefono'];
  $username = $_POST['username'];
  $password = password_hash($username, PASSWORD_DEFAULT);
  $responsabile = $_POST['responsabile'];
  if(mysqli_fetch_assoc(mysqli_query($db,"SELECT operatore.attivo FROM operatore WHERE id = '$id';"))['attivo'] == 1){
    $attivo = 1;
  }else{
    $attivo = 0;
  }


  $queryDenominazione = mysqli_query($db, "SELECT * FROM operatore WHERE operatore.username = '$username'");
  $myDenominazione = mysqli_query($db, "SELECT * FROM operatore WHERE operatore.id = '$id'");

  if (mysqli_num_rows($queryDenominazione) > 0 and $username!=mysqli_fetch_assoc($myDenominazione)['username']){ //controllo che non ci siano altri volontari con lo stesso username come quella, unica eccezzione se stesso
    $_SESSION['errore'] = "mOperatore";
    header("location: gestioneOperatori.php");
    mysqli_close($db);
  }else{

    $query = "UPDATE operatore SET nome = '$nome', cognome = '$cognome', dataNascita = '$dataNascita', sesso = '$sesso', telefono = '$telefono', username = '$username', password = '$password', utente = '$responsabile', attivo = $attivo WHERE id = $id;";
    $volontari = mysqli_query($db, $query);
    header("location: gestioneOperatori.php");
    mysqli_close($db);
  }
?>