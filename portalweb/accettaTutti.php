<?php
include("./session.php");
check_auth();

$idOperatore = (int)$_SESSION['ID'];

/*
  Accetta tutte le missioni disponibili
  (non ancora assegnate) da oggi in poi.
  Adatta i nomi dei campi se nel DB sono diversi.
*/

mysqli_query($db, "
UPDATE missione
SET id_operatore = $idOperatore
WHERE id_operatore IS NULL
AND dataServizio >= CURDATE()
");

echo "OK";
