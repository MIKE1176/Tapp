<?php
  include("session.php");
  check_auth();

  if($_SESSION['auth']!="AMMINISTRATIVO"){
    header("location: index.php");
  }

  // Recupero i dati
  $id = $_POST['idLuogo'];
  $denominazione = $_POST['denominazione'];
  $indirizzo = $_POST['indirizzo'];
  $civico = $_POST['civico'];
  $citta = strtoupper($_POST['citta']);
  $note = mysqli_real_escape_string($db,$_POST['note']);
  if(mysqli_fetch_assoc(mysqli_query($db,"SELECT luogo.attivo FROM luogo WHERE id = '$id';"))['attivo'] == 1){
    $attivo = 1;
  }else{
    $attivo = 0;
  }


  $queryDenominazione = mysqli_query($db, "SELECT * FROM luogo WHERE luogo.denominazione = '$denominazione'");
  $myDenominazione = mysqli_query($db, "SELECT * FROM luogo WHERE luogo.id = '$id'");

  if (mysqli_num_rows($queryDenominazione) > 0 and $denominazione!=mysqli_fetch_assoc($myDenominazione)['denominazione']){ //controllo che non ci siano altre denominazioni come quella, unica eccezzione se stesso
    $_SESSION['errore'] = "mLuogo";
    header("location: gestioneLuoghi.php");
    mysqli_close($db);
  }else{

    $query = "UPDATE luogo SET denominazione = '$denominazione', indirizzo = '$indirizzo', civico = '$civico', citta = '$citta', note = '$note', attivo = $attivo WHERE id = $id;";
    $luoghi = mysqli_query($db, $query);
    header("location: gestioneLuoghi.php");
    mysqli_close($db);
  }
?>