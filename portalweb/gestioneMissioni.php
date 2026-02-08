<?php
include("./session.php");
check_auth();

$idOperatore = $_SESSION['ID'];

$data = $_GET['data'] ?? date("Y-m-d");

$stmt = $db->prepare("
SELECT m.data,
       m.tipo,
       m.durata,
       u.nome,
       u.cognome,
       l.denominazione luogo
FROM missione m
JOIN turno t ON m.id_turno = t.ID
JOIN utente u ON m.id_utente = u.ID
LEFT JOIN luogo l ON m.id_destinazione = l.ID
WHERE t.id_operatore = ?
AND DATE(m.data)=?
ORDER BY m.data
");

$stmt->bind_param("is", $idOperatore, $data);
$stmt->execute();
$res = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Gestione missioni</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

<?php include("./navbar.php"); ?>

<div class="container my-3">

<h3 class="mb-3">Missioni del giorno</h3>

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
    echo "<p>Nessuna missione assegnata.</p>";
}

while ($r = $res->fetch_assoc()) {

    $ora = date("H:i", strtotime($r['data']));

    echo "
    <div class='col-12 mb-3'>
      <div class='card shadow p-3'>
        <h5>{$r['cognome']} {$r['nome']}</h5>
        <p><b>Tipo:</b> {$r['tipo']}</p>
        <p><b>Ora:</b> $ora</p>
        <p><b>Destinazione:</b> {$r['luogo']}</p>
      </div>
    </div>";
}
?>

</div>
</div>

</body>
</html>
