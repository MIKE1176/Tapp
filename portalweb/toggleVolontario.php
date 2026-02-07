<?php

include("./config.php");

$id = $_POST['idVolontario'];

if(mysqli_fetch_assoc(mysqli_query($db,"SELECT volontario.attivo FROM volontario WHERE id = '$id';"))['attivo'] == 1){
    $val = 0;
}else{
    $val = 1;
}

$query = "UPDATE volontario SET volontario.attivo = $val WHERE id = '$id';";
$automezzo = mysqli_query($db, $query);

mysqli_close($db);

header("location: gestioneVolontari.php");
