<?php
  include("session.php");
  check_auth(); // Se non loggato o non attivo, scappa e va al login

  if($_SESSION['auth']!="AMMINISTRATIVO"){
    header("location: index.php");
  }


  // Recupero i dati dal form aggiungiMezzo
  $targa = $_POST['targa'];
  $codiceMezzo = strtoupper($_POST['codiceMezzo']);
  $attivo = 1;


  if(mysqli_num_rows(mysqli_query($db, "SELECT * FROM automezzo WHERE codiceMezzo = '$codiceMezzo'")) > 0){
    $_SESSION['errore'] = "codiceMezzo";
    header("location: gestioneMezzi.php");
    mysqli_close($db);
  }elseif(mysqli_num_rows(mysqli_query($db, "SELECT * FROM automezzo WHERE targa = '$targa'")) > 0){
    $_SESSION['errore'] = "targa";
    header("location: gestioneMezzi.php");
    mysqli_close($db);
  }else{
    //immagine definitiva (inserisce un'immagine provvisoria con il nome=codiceMezzo, così, quando si avrà l'immagine definitiva, basta cambiare solo l'immagine)
    $img = "../assets/mezzi/";

    if (file_exists($img.$codiceMezzo.".png")) { //controlla se il file esiste già, sennò lo crea
      $img = $img.$codiceMezzo.".png";
    }else{
      copy($img."auto.png", $img.$codiceMezzo.".png");
      $img=$img.$codiceMezzo;
    }

    


    $query = "insert into automezzo values('$targa','$codiceMezzo','$img','$attivo')";
    $automezzi = mysqli_query($db, $query);
    header("location: gestioneMezzi.php");
    mysqli_close($db);
  }
?>