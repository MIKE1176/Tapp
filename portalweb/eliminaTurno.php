<?php
include("./session.php");

if(!isset($_POST['idTurno'])){
    header("Location: mieiTurni.php");
    exit;
}

$idTurno = (int)$_POST['idTurno'];
$idUtente = (int)$_SESSION['ID'];

// controllo che sia suo
$check = mysqli_query($db, "
  SELECT ID
  FROM turno
  WHERE ID=$idTurno
  AND id_operatore=$idUtente
");

if(mysqli_num_rows($check)==0){
    die("Operazione non consentita.");
}

// elimina
mysqli_query($db, "
  DELETE FROM turno
  WHERE ID=$idTurno
");

header("Location: mieiTurni.php");
exit;
