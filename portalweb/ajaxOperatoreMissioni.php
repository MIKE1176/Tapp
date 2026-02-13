<?php
include("session.php");
$idOp = $_SESSION['ID'];
$startDate = $_GET['startDate'];

// 1. Carichiamo la configurazione orari dal file JSON
$configJson = file_get_contents('../config_orari.json');
$config = json_decode($configJson, true);

// Recuperiamo i parametri tecnici (default 0 se mancano)
$tempoArrivo = intval($config['tempo_arrivo_obiettivo_min'] ?? 0);
$distanzaObiettivo = intval($config['distanza_obiettivo_min'] ?? 0);

$hasAnyMission = false;
$output = "";

// Set locale per avere i giorni in italiano (se il server lo supporta)
setlocale(LC_TIME, 'it_IT.UTF-8', 'it_IT', 'it');

for ($i = 0; $i < 7; $i++) {
    $currentDate = date('Y-m-d', strtotime("$startDate + $i days"));
    
    // Nome del giorno completo (es: Lunedì 16 Febbraio)
    // Usiamo IntlDateFormatter perché strftime è deprecato nelle versioni recenti di PHP
    $fmt = new IntlDateFormatter('it_IT', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
    $fmt->setPattern('EEEE d MMMM');
    $titoloGiorno = ucfirst($fmt->format(strtotime($currentDate)));
    
    // Query per le missioni dell'operatore loggato
    $sql = "SELECT m.*, u.nome, u.cognome, u.noteUtente,
                   dest.denominazione as nome_dest, part.denominazione as nome_part
            FROM missione m
            LEFT JOIN utente u ON m.id_utente = u.ID
            LEFT JOIN luogo dest ON m.id_destinazione = dest.ID
            LEFT JOIN luogo part ON m.id_obiettivo = part.ID
            WHERE m.id_operatore = $idOp 
            AND DATE(m.data) = '$currentDate'
            ORDER BY m.data ASC";
            
    $res = mysqli_query($db, $sql);
    
    if (mysqli_num_rows($res) > 0) {
        $hasAnyMission = true;
        $output .= "<div class='day-group mb-4'>";
        $output .= "  <div class='day-header text-primary border-bottom border-2 pb-1 mb-3 fw-bold text-uppercase' style='letter-spacing:1px; font-size:1.1rem;'>
                        $titoloGiorno 
                        <span class='badge bg-primary float-end rounded-pill'>".mysqli_num_rows($res)."</span>
                      </div>";
        
        while ($row = mysqli_fetch_assoc($res)) {
            $timestampMissione = strtotime($row['data']);
            $oraAppuntamento = date("H:i", $timestampMissione);
            $tipo = $row['tipo'];
            
            // 2. Calcolo Orario di Partenza Suggerito
            if ($tipo === 'ANDATA') {
                // Sottraiamo tempo arrivo + distanza
                $minutiDaSottrarre = $tempoArrivo + $distanzaObiettivo;
            } else {
                // Solo tempo arrivo
                $minutiDaSottrarre = $tempoArrivo;
            }
            $oraPartenza = date("H:i", strtotime("-$minutiDaSottrarre minutes", $timestampMissione));

            $classeTipo = ($tipo == 'RITORNO') ? 'ritorno' : '';
            $idCollapse = "note_" . $row['ID'];
            
            $output .= "
            <div class='card mission-card $classeTipo mb-3 shadow-sm'>
                <div class='card-body p-3'>
                    <div class='d-flex justify-content-between align-items-center mb-2'>
                        <span class='badge bg-dark text-white p-2'>
                            <i class='bi bi-truck me-1'></i> PARTENZA: $oraPartenza
                        </span>
                        <span class='small fw-bold text-muted'>$tipo</span>
                    </div>

                    <div class='fw-bold h5 mb-1'>{$row['nome']} {$row['cognome']}</div>
                    
                    <div class='text-primary mb-3 fw-bold'>
                        <i class='bi bi-clock-fill me-1'></i> $oraAppuntamento
                    </div>

                    <div class='small text-secondary mb-3'>
                        <div class='mb-1'><i class='bi bi-geo-alt-fill text-danger'></i> <b>DA:</b> " . ($row['nome_part'] ?: 'Indirizzo Utente') . "</div>
                        <div><i class='bi bi-flag-fill text-success'></i> <b>A:</b> " . ($row['nome_dest'] ?: 'Indirizzo Utente') . "</div>
                    </div>

                    <div class='border-top pt-2'>
                        <button class='btn btn-sm btn-light w-100 text-secondary fw-bold' type='button' 
                                data-bs-toggle='collapse' data-bs-target='#$idCollapse'>
                            <i class='bi bi-journal-text me-1'></i> NOTE UTENTE
                        </button>
                        <div id='$idCollapse' class='collapse mt-2'>
                            <div class='p-3 bg-light rounded border small italic text-dark'>
                                " . (!empty($row['noteUtente']) ? nl2br(htmlspecialchars($row['noteUtente'])) : "Nessuna nota specifica per questo utente.") . "
                            </div>
                        </div>
                    </div>
                </div>
            </div>";
        }
        $output .= "</div>";
    }
}

if (!$hasAnyMission) {
    echo "
    <div class='empty-state py-5 text-center'>
        <i class='bi bi-calendar2-check text-muted' style='font-size: 4rem;'></i>
        <h4 class='text-muted mt-3'>Nessuna missione assegnata</h4>
        <p class='text-secondary'>Per questo periodo non risultano trasporti a tuo carico.</p>
    </div>";
} else {
    echo $output;
}
?>