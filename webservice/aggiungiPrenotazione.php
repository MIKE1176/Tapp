<?php
    include("./session.php");
    check_auth();

    // Carichiamo la configurazione per validazione sicurezza lato server
    $configPath = '../config_orari.json';
    $config = json_decode(file_get_contents($configPath), true);
    $minGG = $config['preavviso_minimo_giorni'];
    $maxGG = $config['limite_massimo_giorni'];

    // Recupero dati dal form
    $idUtente = $_SESSION['ID'];
    $idDestinazione = $_POST['idLuogo']; // Corrisponde a id="idluogo" nel form
    
    // Costruzione Data e Ora
    // Il campo 'oraScelta' arriva come "HH:mm" (es. "09:30")
    $dataInvio = $_POST['annoArrivo'] . '-' . $_POST['meseArrivo'] . '-' . $_POST['giornoArrivo'];
    $oraInvio = $_POST['oraScelta'] . ':00';
    $dataCompleta = $dataInvio . ' ' . $oraInvio;

    // Costruzione Durata
    // Formato HH:mm (es. "01:30")
    $durata = $_POST['oreDurata'] . ':' . $_POST['minutiDurata'];

    $statoCompilazione="INSERITA";

    // --- VALIDAZIONE SICUREZZA LATO SERVER ---
    $dataPrenotata = new DateTime($dataInvio);
    $oggi = new DateTime();
    $oggi->setTime(0,0,0); // Reset orario per confrontare solo i giorni

    // Creiamo le date limite basate sul JSON
    $dataMinima = clone $oggi;
    $dataMinima->modify("+" . $minGG . " days");

    $dataMassima = clone $oggi;
    $dataMassima->modify("+" . $maxGG . " days");

    if ($dataPrenotata < $dataMinima || $dataPrenotata > $dataMassima) {
        header("location: home.php?error=data_non_valida");
        exit;
    }
    // -----------------------------------------

    // Esecuzione Query
    $sql = "INSERT INTO missione (id_utente, id_obiettivo, id_destinazione, data, durata,statoCompilazione) 
            VALUES ('$idUtente', 1, '$idDestinazione', '$dataCompleta', '$durata', '$statoCompilazione')";

    if(mysqli_query($db, $sql)){
        mysqli_close($db);
        header("location: home.php?success=1");
        exit;
    } else {
        // Gestione errore
        mysqli_close($db);
        header("location: home.php?error=db_error");
        exit;
    }
?>