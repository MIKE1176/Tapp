<?php
    include("session.php");
    check_auth();

    if($_SESSION['auth']!="AMMINISTRATIVO"){
        header("location: index.php");
        exit();
    }

    $id = mysqli_real_escape_string($db, $_POST['idOperatore']);


    $result = mysqli_query($db, "SELECT attivo FROM operatore WHERE id = '$id'");
    $row = mysqli_fetch_assoc($result);

    if($row){
        $val = ($row['attivo'] == 1) ? 0 : 1;

        $query = "UPDATE operatore SET attivo = $val WHERE id = '$id'";
        mysqli_query($db, $query);
    }

    mysqli_close($db);
    header("location: gestioneOperatori.php");
    exit();
?>