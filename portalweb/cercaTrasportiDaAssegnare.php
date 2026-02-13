<?php
include('./session.php');

$idOperatoreLoggato = $_SESSION['ID'];
$nomeOperatore = $_SESSION['nome'] . " " . $_SESSION['cognome'];

$dataRicerca = mysqli_real_escape_string($db, $_GET['date']);

// Query aggiornata: esclude il tipo RITORNO
$sql = "SELECT m.*, 
               u.nome, u.cognome, u.indirizzo as u_via, u.civico as u_civ, u.citta as u_cit,
               dest.denominazione as nome_destinazione,
               part.denominazione as nome_partenza
        FROM missione m
        LEFT JOIN utente u ON m.id_utente = u.ID
        LEFT JOIN luogo dest ON m.id_destinazione = dest.ID
        LEFT JOIN luogo part ON m.id_obiettivo = part.ID
        WHERE DATE(m.data) = '$dataRicerca' 
        AND m.statoCompilazione = 'INSERITA'
        AND m.tipo != 'RITORNO'
        ORDER BY m.data ASC";

$result = mysqli_query($db, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // --- CALCOLI ORARI E DURATA ---
        $andataTimestamp = strtotime($row['data']);
        $oraAndata = date("H:i", $andataTimestamp);
        
        $durataParts = explode(':', $row['durata']);
        $secondiDurata = ($durataParts[0] * 3600) + ($durataParts[1] * 60);
        $oraRitorno = date("H:i", $andataTimestamp + $secondiDurata);
        $durataFormattata = intval($durataParts[0]) . "h " . intval($durataParts[1]) . "m";

        $partenza = ($row['id_obiettivo'] == 1) ? $row['u_via']." ".$row['u_civ'].", ".$row['u_cit'] : $row['nome_partenza'];
        $destinazione = ($row['id_destinazione'] == 1) ? $row['u_via']." ".$row['u_civ'].", ".$row['u_cit'] : $row['nome_destinazione'];

        // --- GESTIONE ANNOTAZIONI E ERRORE NULL ---
        $annotazioniEsistenti = $row['annotazioni'] ?? ""; // Se null diventa stringa vuota
        $testoRifiuto = "L'autista $nomeOperatore non disponibile";
        $giaRifiutata = (strpos($annotazioniEsistenti, $testoRifiuto) !== false);

        echo '
        <div class="card mission-card shadow-sm mb-2">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="text-primary fw-bold h5 mb-0">'.$oraAndata.'</div>
                    <div class="badge bg-light text-dark border">'.$durataFormattata.'</div>
                </div>
                
                <div class="fw-bold text-dark mb-2">
                     <i class="bi bi-person-circle me-1"></i> '.$row['nome'].' '.$row['cognome'].'
                </div>
                
                <div class="small text-secondary mb-2">
                    <div class="mb-1"><i class="bi bi-geo-alt-fill text-danger"></i> <b>DA:</b> '.$partenza.'</div>
                    <div class="mb-1"><i class="bi bi-flag-fill text-success"></i> <b>A:</b> '.$destinazione.'</div>
                    <div class="mt-2 p-1 bg-warning-subtle rounded text-center fw-bold text-dark border border-warning-subtle">
                         <i class="bi bi-clock-history"></i> RITORNO PREVISTO: '.$oraRitorno.'
                    </div>
                </div>

                <div class="p-2 bg-light rounded border mb-3 small">
                    <b class="text-uppercase" style="font-size: 0.7rem;">Annotazioni:</b><br>
                    <span class="text-muted">'.(!empty($annotazioniEsistenti) ? nl2br(htmlspecialchars($annotazioniEsistenti)) : "Nessuna annotazione.").'</span>
                </div>
                
                <input type="text" class="form-control form-control-sm border-primary mb-3" 
                id="nota_'.$row['ID'].'" placeholder="Aggiungi una nota (opzionale)...">

                <div class="row g-2">';
                    if (!$giaRifiutata) {
                        echo '<div class="col-6">
                            <button class="btn btn-outline-danger btn-action w-100 rounded-3 shadow-sm" onclick="gestisciMissione('.$row['ID'].', \'rifiuta\')">
                                <i class="bi bi-x-circle"></i><span>No Disp.</span>
                            </button>
                        </div>';
                    }
                    echo '<div class="col-'.($giaRifiutata ? '12' : '6').'">
                        <button class="btn btn-success btn-action w-100 rounded-3 shadow-sm" onclick="gestisciMissione('.$row['ID'].', \'accetta\')">
                            <i class="bi bi-check-circle-fill"></i><span>Accetta</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>';
    }
}
?>