<?php
include("./config.php");

$idPaziente = $_POST['disattivaPaziente'];

if(mysqli_fetch_assoc(mysqli_query($db,"SELECT paziente.attivo FROM paziente WHERE id = '$idPaziente';"))['attivo'] == 1){
    $val = 0;
}else{
    $val = 1;
}

$query = "UPDATE paziente SET paziente.attivo = $val WHERE id = '$idPaziente';";
$paziente = mysqli_query($db, $query);

mysqli_close($db);

header("location: gestionePazienti.php");
