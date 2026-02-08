<?php
include("./session.php");
check_auth();

$idMissione=$_POST['id'] ?? null;
$idAutista=$_SESSION['ID'];

if(!$idMissione) exit;

$db->begin_transaction();

// prendiamo turno compatibile
$qTurno="
SELECT t.ID
FROM turno t
JOIN missione m ON m.data BETWEEN t.dataInizio AND t.dataFine
WHERE m.ID=?
AND t.id_operatore=?
FOR UPDATE
";

$stmt=$db->prepare($qTurno);
$stmt->bind_param("ii",$idMissione,$idAutista);
$stmt->execute();
$res=$stmt->get_result();

if(!$turno=$res->fetch_assoc()){
 $db->rollback();
 exit;
}

$idTurno=$turno['ID'];

$upd="
UPDATE missione
SET id_turno=?,
    statoCompilazione='ASSEGNATA'
WHERE ID=?
AND statoCompilazione='INSERITA'
";

$stmt=$db->prepare($upd);
$stmt->bind_param("ii",$idTurno,$idMissione);

if(!$stmt->execute()){
 $db->rollback();
 exit;
}

$db->commit();
