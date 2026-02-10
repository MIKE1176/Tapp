<?php
  include("session.php");
  check_auth();

  if($_SESSION['auth']!="AMMINISTRATIVO"){
    header("location: index.php");
    exit();
  }

  // Recupero i dati
  $id = mysqli_real_escape_string($db, $_POST['idOperatore']);
  $nome = mysqli_real_escape_string($db, firstUpperSentences($_POST['nome']));
  $cognome = mysqli_real_escape_string($db, firstUpperSentences($_POST['cognome']));
  $dataNascita = mysqli_real_escape_string($db, $_POST['dataNascita']);
  $sesso = mysqli_real_escape_string($db, $_POST['sesso']);
  $telefono = mysqli_real_escape_string($db, $_POST['telefono']);
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $responsabile = mysqli_real_escape_string($db, $_POST['responsabile']);

  $checkQuery = "SELECT ID FROM operatore WHERE username = '$username' AND ID != '$id'";
  $res = mysqli_query($db, $checkQuery);

  if (mysqli_num_rows($res) > 0){ //controllo che non ci siano altri volontari con lo stesso username come quella, unica eccezzione se stesso
    $_SESSION['errore'] = "mOperatore";
    
  }else{

    $query = "UPDATE operatore SET 
              nome = '$nome', 
              cognome = '$cognome', 
              dataNascita = '$dataNascita', 
              sesso = '$sesso', 
              telefono = '$telefono', 
              username = '$username', 
              utente = '$responsabile' 
              WHERE ID = $id";
    mysqli_query($db, $query);

  }
  header("location: gestioneOperatori.php");
  mysqli_close($db);
  exit();
?>