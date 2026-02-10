<?php
    include("./session.php");
    check_auth(); // Se NON sono loggato, mi manda a accedi.php
    $idUtente = $_SESSION['ID'];
    
    $configPath = '../config_orari.json';
    $config = json_decode(file_get_contents($configPath), true);
    $limiteDisdetta = isset($config['limite_disdetta_giorni']) ? (int)$config['limite_disdetta_giorni'] : 1; // Default 2 se manca nel JSON
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#BB0000">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="manifest.json" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">
    <script src="./js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap');
    </style>

    <link rel="icon" href="../assets/favicon.ico" type="image/x-icon">
    <title>Le tue prenotazioni</title>
</head>

<body class="ubuntu-regular" style="background-color: rgb(187, 0, 0);">

    <div class="d-flex align-items-center m-4 bg-danger-subtle rounded-5 p-1 border border-2 border-white">
        <button class="btn btn-danger rounded-5 w-100 shadow-none border-0" onclick="window.location.href='home.php'">
            <div class="hstack d-flex align-items-center">
                <i class="bi bi-arrow-bar-left h1 m-0 p-0"></i>
                <div class="vr ms-3"></div>
                <div class="d-flex w-100 justify-content-center">
                    <p class="m-0 p-0 h1 fw-bold text-uppercase">Indietro</p>
                </div>
            </div>
        </button>
    </div>

    <div class="m-4 bg-light rounded-5 p-4 shadow-lg">
        <h2 class="fw-bold text-danger text-uppercase">Prenotazioni attive</h2>
        <hr>
        <div class="bg-danger rounded-3 p-3 mb-2 border border-white text-center shadow-sm">
            <p class="m-0 small fw-bold text-white">
                <i class="bi bi-exclamation-circle-fill"></i> 
                Fatti trovare pronto circa 15 minuti prima dell'orario indicato.
            </p>
        </div>
        <div class="vstack gap-3 d-flex justify-content-center py-4">
        <?php
            // Query per le missioni future
            $query = mysqli_query($db, "SELECT missione.id AS idPrenotazione, obiettivo.denominazione AS nomeObiettivo, destinazione.denominazione AS nomeDestinazione, data, TIME_FORMAT(`durata`, '%H:%i') AS durata, statoCompilazione, tipo FROM missione JOIN luogo AS obiettivo ON id_obiettivo = obiettivo.id JOIN luogo AS destinazione ON id_destinazione = destinazione.id WHERE data >= NOW() AND id_utente = '$idUtente' AND tipo!='RITORNO' ORDER BY data ASC");

            if (mysqli_num_rows($query) != 0) {
                // Prepariamo la data di oggi "pulita" (senza ore/minuti)
                $oggi = new DateTime();
                $oggi->setTime(0, 0, 0);

                while ($row = mysqli_fetch_assoc($query)) {
                    $idPrenotazione = $row['idPrenotazione'];
                    $nomeObiettivo = (isset($row['nomeObiettivo'])) ? $row['nomeObiettivo'] : "La tua abitazione";
                    if($nomeObiettivo == "Abitazione dell'utente"){
                        $nomeObiettivo="La tua abitazione";
                    }
                    $nomeDestinazione = $row['nomeDestinazione'];
                    
                    $dataOggetto = DateTime::createFromFormat('Y-m-d H:i:s', $row['data']);
                    $dataFormattata = $dataOggetto->format('d/m/Y H:i');

                    $durata = $row['durata']; // Ora vale ad esempio "01:30"
                    $durataOggetto = DateTime::createFromFormat('H:i', $durata);
                    $durataOre = $durataOggetto->format('H');
                    $durataMinuti = $durataOggetto->format('i');

                    $statoCompilazione = $row['statoCompilazione'];

                    // Calcolo giorni di differenza sulla data pura (senza ore)
                    $dataPrenotazionePura = clone $dataOggetto;
                    $dataPrenotazionePura->setTime(0, 0, 0);
                    
                    // Usiamo questo metodo per avere la differenza esatta di giorni di calendario
                    $intervallo = $oggi->diff($dataPrenotazionePura);
                    $giorniMancanti = (int)$intervallo->format('%r%a'); // %r include il segno (+ o -)

                    $dataRientro = clone $dataOggetto;

                    // Aggiungiamo le ore e i minuti della durata
                    $dataRientro->modify("+{$durataOre} hours");
                    $dataRientro->modify("+{$durataMinuti} minutes");

                    // Ora puoi formattarla come preferisci
                    $orarioRientro = $dataRientro->format('H:i');

                    // Logica Annullamento
                    $htmlAnnulla = "";
                    if ($statoCompilazione=="ANNULLATA") {
                        // Se mancano PIÙ giorni del limite (es. se limite è 1, deve mancare almeno 2 giorni)
                        $htmlAnnulla = <<<HTML
                        <div class="alert alert-error small py-2 mt-2 mb-0 rounded-3 text-center border-0 fw-bold">
                            <p id="avvisoData" class="text-danger text-center fw-bold small mt-2 mb-2">⚠️ PRENOTAZIONE ANNULLATA</p>
                        </div>
HTML;
                    } else if($giorniMancanti > $limiteDisdetta){
                        // Se mancano 1 o 0 giorni
                        $htmlAnnulla = <<<HTML
                        <form action="annullaPrenotazione.php" method="post" class="mt-3">
                            <input type="hidden" name="idPrenotazione" value="$idPrenotazione">
                            <button type="submit" class="btn btn-outline-danger w-100 rounded-3 fw-bold py-2" onclick="return confirm('Sei sicuro di voler annullare?')">ANNULLA PRENOTAZIONE</button>
                        </form>
                        
HTML;
                    }else {
                        // Se mancano 1 o 0 giorni
                        $htmlAnnulla = <<<HTML
                        <div class="alert alert-warning small py-2 mt-3 mb-0 rounded-3 text-center border-0 fw-bold">
                            <i class="bi bi-info-circle"></i> Non è più possibile disdire.<br>
                        </div>
HTML;
                    }

                    echo <<<HTML
                    <div class="card p-3 w-100 border-0 rounded-4 bg-danger-subtle shadow-sm">
                        <div class="card-body p-1">
                            <h4 class="fw-bold mb-1 text-danger">$dataFormattata</h4>
                            <h5 class="mb-3 text-dark">$nomeDestinazione</h5>
HTML;
                            if ($statoCompilazione!="ANNULLATA") {
                                echo <<<HTML
                            <div class="small text-secondary mb-3">
                                <i class="bi bi-clock"></i> Durata: <strong>$durataOre ore e $durataMinuti minuti.</strong><br>
                                <i class="bi bi-geo-alt"></i> Da: $nomeObiettivo<br>
                                <i class="bi bi-info-circle"></i> Stato: $statoCompilazione<br><br>
                                <i class="bi bi-geo-fill"></i>Verremo a riprenderti alle  $orarioRientro
                            </div>
HTML;                       
                            }
                            echo <<<HTML
                            $htmlAnnulla
                        </div>
                    </div>
HTML;
                }
            } else {
                echo '<p class="text-center opacity-50 p-4">Non hai nessuna prenotazione attiva</p>';
            }
        ?>
        </div>

        <h2 class="fw-bold mt-4 text-uppercase">Storico</h2>
        <hr>
        <div class="vstack gap-2 d-flex justify-content-center py-4">
        <?php
            $query_storico = mysqli_query($db, "SELECT missione.id AS idPrenotazione, obiettivo.denominazione AS nomeObiettivo, destinazione.denominazione AS nomeDestinazione, data, statoCompilazione FROM missione JOIN luogo AS obiettivo ON id_obiettivo = obiettivo.id JOIN luogo AS destinazione ON id_destinazione = destinazione.id WHERE data < NOW() AND tipo='ANDATA' AND id_utente = '$idUtente' ORDER BY data DESC LIMIT 5");
            
            if (mysqli_num_rows($query_storico) != 0) {
                while ($row = mysqli_fetch_assoc($query_storico)) {
                    $nomeDestinazione = $row['nomeDestinazione'];
                    $data = DateTime::createFromFormat('Y-m-d H:i:s', $row['data'])->format('d/m/Y');
                    $statoCompilazione=$row['statoCompilazione'];
                    echo <<<HTML
                    <div class="card p-2 w-100 border-0 rounded-4 bg-secondary-subtle opacity-75">
                        <div class="card-body py-1 small">
                            <span class="fw-bold">$data</span> - $nomeDestinazione [$statoCompilazione]
                        </div>
                    </div>
HTML;
                }
            } else {
                echo '<p class="text-center opacity-50 small">Nessuna prenotazione passata</p>';
            }
        ?>
        </div>
    </div>
</body>

</html>