<?php
include("./session.php");
check_auth();

$idOperatore = $_SESSION['ID'];

$data = $_GET['data'] ?? date("Y-m-d");

$stmt = $db->prepare("
SELECT t.dataInizio,
       t.dataFine,
       t.note,
       a.codiceMezzo
FROM turno t
JOIN automezzo a ON t.automezzo = a.targa
WHERE t.id_operatore = ?
AND DATE(t.dataInizio)=?
ORDER BY t.dataInizio
");

$stmt->bind_param("is", $idOperatore, $data);
$stmt->execute();
$res = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Gestione turni</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

<?php include("./navbar.php"); ?>

<div class="container my-3">

<h3 class="mb-3">Turni del giorno</h3>

<div class="mb-3">

<label class="form-label fw-bold">Seleziona giorno</label>

<input type="date"
       class="form-control"
       value="<?=$data?>"
       onchange="cambiaData(this.value)">

</div>

<script>
function cambiaData(d){
  window.location = "?data=" + d;
}
</script>

<div class="row">

<?php
if ($res->num_rows == 0) {
    echo "<p>Nessun turno presente.</p>";
}

while ($r = $res->fetch_assoc()) {

    $inizio = date("H:i", strtotime($r['dataInizio']));
    $fine   = date("H:i", strtotime($r['dataFine']));

    echo "
    <div class='col-12 mb-3'>
      <div class='card shadow p-3'>
        <h5>Mezzo: {$r['codiceMezzo']}</h5>
        <p><b>Orario:</b> $inizio → $fine</p>
        <p class='text-muted'>{$r['note']}</p>
      </div>
    </div>";
}
?>

</div>
</div>

</body>
</html>
