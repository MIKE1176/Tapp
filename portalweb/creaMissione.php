<?php
include("./session.php");

$id_turno=$_POST['id_turno'];
$id_utente=$_POST['id_utente'];
$tipo=$_POST['tipo'];
$note=$_POST['annotazioni'];

mysqli_query($db,"
INSERT INTO missione
(statoCompilazione,data,tipo,annotazioni,id_turno,id_utente)
VALUES('ASSEGNATA',NOW(),'$tipo','$note',$id_turno,$id_utente)
");

header("location: missioni.php");
