<?php
    include("session.php");
    check_auth();

    if($_SESSION['auth']!="AMMINISTRATIVO"){
    header("location: index.php");
    }

    $id = $_POST['idLuogo'];

    if(mysqli_fetch_assoc(mysqli_query($db,"SELECT luogo.attivo FROM luogo WHERE id = '$id';"))['attivo'] == 1){
    $val = 0;
    }else{
    $val = 1;
    }

    $query = "UPDATE luogo SET luogo.attivo = $val WHERE id = '$id';";
    $luogo = mysqli_query($db, $query);

    mysqli_close($db);

    header("location: gestioneLuoghi.php");
?>
