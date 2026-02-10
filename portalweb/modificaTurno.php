<?php
    include("./session.php");
    check_auth();

    $config = json_decode(file_get_contents('../config_orari.json'), true);
    $durataMinuti = $config['slot_turni'] ?? 60;

    $idTurno = (int)$_POST['idTurno'];
    $idOperatore = (int)$_SESSION['ID'];
    $slotRaw = $_POST['slotSelezionati'] ?? '';
    $data    = $_POST['dataTurno'] ?? '';

    if (empty($slotRaw) || empty($data) || $idTurno === 0) {
        header("Location: mieiTurni.php?error=missing");
        exit;
    }

    $slots = explode(",", $slotRaw);
    sort($slots); 
    $inizio = $data . " " . $slots[0] . ":00";
    $dateFine = new DateTime($data . " " . end($slots) . ":00");
    $dateFine->modify("+$durataMinuti minutes");
    $fine = $dateFine->format('Y-m-d H:i:s');

    // Eseguiamo l'UPDATE verificando sempre che il turno sia dell'operatore loggato
    $stmt = mysqli_prepare($db, "UPDATE turno SET dataInizio = ?, dataFine = ? WHERE ID = ? AND id_operatore = ?");
    mysqli_stmt_bind_param($stmt, "ssii", $inizio, $fine, $idTurno, $idOperatore);
    $res = mysqli_stmt_execute($stmt);

    header("Location: mieiTurni.php?" . ($res ? "updated=1" : "error=sql"));
    exit;
?>