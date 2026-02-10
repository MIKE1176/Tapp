<?php
    include("session.php");
    check_auth();

    if($_SESSION['auth']!="AMMINISTRATIVO"){
        header("location: index.php");
        exit();
    }

    $idUtente = mysqli_real_escape_string($db, $_POST['idUtente']);

    if(mysqli_fetch_assoc(mysqli_query($db,"SELECT utente.attivo FROM utente WHERE id = '$idUtente';"))['attivo'] == 1){
        $val = 0;
    }else{
        $val = 1;
    }

    $query = "UPDATE utente SET utente.attivo = $val WHERE id = '$idUtente';";
    mysqli_query($db, $query);

    mysqli_close($db);
    header("location: gestioneUtenti.php");
    exit();
?>