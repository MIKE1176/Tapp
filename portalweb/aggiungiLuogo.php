<?php
  include("session.php");
  check_auth();

  if($_SESSION['auth']!="AMMINISTRATIVO"){
    header("location: index.php");
  }

  // Recupero i dati
  $denominazione = $_POST['denominazione'];
  $indirizzo = $_POST['indirizzo'];
  $civico = $_POST['civico'];
  $citta = strtoupper($_POST['citta']);
  $note = mysqli_real_escape_string($db,$_POST['note']);
  $attivo = 1;


  if (mysqli_num_rows(mysqli_query($db, "SELECT * FROM luogo WHERE luogo.denominazione = '$denominazione'")) > 0){
    $_SESSION['errore'] = "aLuogo";
    header("location: gestioneLuoghi.php");
    mysqli_close($db);
  }else{

    $query = "insert into luogo(denominazione,indirizzo,civico,citta,note,attivo) values('$denominazione','$indirizzo','$civico','$citta','$note',$attivo);";
    $luoghi = mysqli_query($db, $query);
    header("location: gestioneLuoghi.php");
    mysqli_close($db);
  }
?>
