<?php
    include("session.php");
    check_auth(); // Se non loggato o non attivo, scappa e va al login

    if($_SESSION['auth']!="AMMINISTRATIVO"){
        header("location: index.php");
    }

    $targa = $_POST['targa'];

    if(mysqli_fetch_assoc(mysqli_query($db,"SELECT automezzo.attivo FROM automezzo WHERE targa = '$targa';"))['attivo'] == 1){
        $val = 0;
    }else{
        $val = 1;
    }

    $query = "UPDATE automezzo SET automezzo.attivo = $val WHERE targa = '$targa';";
    $automezzo = mysqli_query($db, $query);

    mysqli_close($db);

    header("location: gestioneMezzi.php");
?>