<?php

include("./config.php");

$id = $_POST['idOperatore'];

if(mysqli_fetch_assoc(mysqli_query($db,"SELECT operatore.attivo FROM operatore WHERE id = '$id';"))['attivo'] == 1){
    $val = 0;
}else{
    $val = 1;
}

$query = "UPDATE operatore SET operatore.attivo = $val WHERE id = '$id';";
$automezzo = mysqli_query($db, $query);

mysqli_close($db);

header("location: gestioneOperatori.php");
