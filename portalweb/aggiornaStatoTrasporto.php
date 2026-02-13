<?php
include('./session.php');
header('Content-Type: application/json');

$idM = intval($_POST['id_missione']);
$azione = $_POST['azione'];
$notaExtra = mysqli_real_escape_string($db, $_POST['nota_extra']);
$idOp = $_SESSION['ID'];
$nomeOp = $_SESSION['nome'] . " " . $_SESSION['cognome'];

// Recupero dati missione corrente
$res = mysqli_query($db, "SELECT * FROM missione WHERE ID = $idM");
$m = mysqli_fetch_assoc($res);
if (!$m) {
    echo json_encode(['success' => false, 'message' => 'Missione non trovata']);
    exit;
}

$annotazioniAttuali = $m['annotazioni'] ?? "";
$idUtente = $m['id_utente'];
$dataMissione = date('Y-m-d', strtotime($m['data']));

if ($azione === 'rifiuta') {
    $separator = !empty($annotazioniAttuali) ? "\n" : "";
    $nuovaNota = $annotazioniAttuali . $separator . "L'autista $nomeOp non disponibile. " . $notaExtra;
    
    $sql = "UPDATE missione SET annotazioni = '".mysqli_real_escape_string($db, $nuovaNota)."' WHERE ID = $idM";
    
    if(mysqli_query($db, $sql)) echo json_encode(['success' => true]);
    else echo json_encode(['success' => false, 'message' => 'Errore nel rifiuto']);

} elseif ($azione === 'accetta') {
    // Calcolo tempi per il turno
    $inizio = date('Y-m-d H:i:s', strtotime($m['data'] . ' -30 minutes'));
    $durataParts = explode(':', $m['durata']);
    $secondiDurata = ($durataParts[0] * 3600) + ($durataParts[1] * 60);
    
    // Cerchiamo se esiste una missione di RITORNO per lo stesso utente nello stesso giorno (ID successivo)
    $sqlRitorno = "SELECT ID FROM missione 
                   WHERE id_utente = $idUtente 
                   AND DATE(data) = '$dataMissione' 
                   AND tipo = 'RITORNO' 
                   AND ID > $idM 
                   LIMIT 1";
    $resRitorno = mysqli_query($db, $sqlRitorno);
    $ritorno = mysqli_fetch_assoc($resRitorno);
    $idRitorno = $ritorno ? $ritorno['ID'] : null;

    // Se c'è un ritorno, il turno deve coprire anche quello (Andata + Durata + eventuale margine)
    // Usiamo il timestamp della fine del ritorno per chiudere il turno
    $fineTimestamp = strtotime($m['data']) + $secondiDurata + (15 * 60);
    $fine = date('Y-m-d H:i:s', $fineTimestamp);

    mysqli_begin_transaction($db);

    try {
        // 1. Creo il turno
        $notaTurno = "Turno creato da missione ID $idM" . ($idRitorno ? " e Ritorno ID $idRitorno" : "");
        $sqlTurno = "INSERT INTO turno (dataInizio, dataFine, id_operatore, note) 
                     VALUES ('$inizio', '$fine', $idOp, '$notaTurno')";
        mysqli_query($db, $sqlTurno);
        $idTurnoCreato = mysqli_insert_id($db);

        // 2. Aggiorno la missione di ANDATA
        $separator = !empty($annotazioniAttuali) ? "\n" : "";
        $notaFinale = $annotazioniAttuali . $separator . "Accettata da $nomeOp. " . $notaExtra;

        $sqlMiss = "UPDATE missione SET 
                    statoCompilazione = 'ASSEGNATA', 
                    id_operatore = $idOp, 
                    id_turno = $idTurnoCreato,
                    annotazioni = '".mysqli_real_escape_string($db, $notaFinale)."'
                    WHERE ID = $idM";
        mysqli_query($db, $sqlMiss);

        // 3. Se esiste il RITORNO, aggiorno anche quello
        if ($idRitorno) {
            $sqlMissRitorno = "UPDATE missione SET 
                                statoCompilazione = 'ASSEGNATA', 
                                id_operatore = $idOp, 
                                id_turno = $idTurnoCreato,
                                annotazioni = CONCAT(IFNULL(annotazioni,''), '\nAccettata automaticamente (Ritorno) da $nomeOp.')
                                WHERE ID = $idRitorno";
            mysqli_query($db, $sqlMissRitorno);
        }

        mysqli_commit($db);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        mysqli_rollback($db);
        echo json_encode(['success' => false, 'message' => 'Errore durante l\'assegnazione: ' . $e->getMessage()]);
    }
}
?>