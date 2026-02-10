<?php
include("./session.php");
check_auth();
// 1. Carica configurazione
$config = json_decode(file_get_contents('../config_orari.json'), true);
$durataMinuti = $config['slot_turni'] ?? 60;

// 2. Recupero dati
$autista = (int)($_POST['idOperatore'] ?? 0);
$automezzo = null;
$slotRaw = $_POST['slotSelezionati'] ?? '';
$data    = $_POST['dataTurno'] ?? '';

if (empty($slotRaw) || empty($data) || $autista === 0) {
    header("Location: mieiTurni.php?error=missing");
    exit;
}

// 3. Elaborazione Orari
$slots = explode(",", $slotRaw);
sort($slots); 

$inizioStr = $data . " " . $slots[0] . ":00";
$ultimoSlotStr = $data . " " . end($slots) . ":00";

// Calcolo la fine reale
$dateFine = new DateTime($ultimoSlotStr);
$dateFine->modify("+$durataMinuti minutes");

$inizio = $inizioStr;
$fine   = $dateFine->format('Y-m-d H:i:s');

// 4. Database
// Assumo che $db sia la variabile di connessione proveniente da session.php
if (!$db) {
    die("Errore di connessione al database");
}

$stmt = mysqli_prepare($db, "INSERT INTO turno (dataInizio, dataFine, id_operatore, automezzo) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssii", $inizio, $fine, $autista, $automezzo);

$res = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: mieiTurni.php?" . ($res ? "success=1" : "error=sql"));
exit;
?>