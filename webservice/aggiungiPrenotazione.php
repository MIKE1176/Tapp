<?php
    include("./session.php");
    check_auth();

    $configPath = '../config_orari.json';
    $config = json_decode(file_get_contents($configPath), true);
    $minGG = $config['preavviso_minimo_giorni'];
    $maxGG = $config['limite_massimo_giorni'];

    $idUtente = $_SESSION['ID'];
    $idDestinazione = $_POST['idLuogo'];
    $durata = $_POST['oreDurata'] . ':' . $_POST['minutiDurata'] . ':00';
    $statoCompilazione = "INSERITA";

    // --+-- CALCOLO COMPLETO ORARI/DATE --+--


    // --- ANDATA ---

    $dataAndata = $_POST['annoArrivo'] . '-' . $_POST['meseArrivo'] . '-' . $_POST['giornoArrivo'];
    $oraAndata = $_POST['oraScelta'] . ':00';
    $andataString = $dataAndata . ' ' . $oraAndata;

    // --- VALIDAZIONE SICUREZZA LATO SERVER ---

    $dataAndataPrenotata = new DateTime($dataAndata);
    
    $oggi = new DateTime();
    $oggi->setTime(0,0,0); // Reset orario per confrontare solo i giorni

    // Creiamo le date limite basate sul JSON
    $dataMinima = clone $oggi;
    $dataMinima->modify("+" . $minGG . " days");

    $dataMassima = clone $oggi;
    $dataMassima->modify("+" . $maxGG . " days");

    if ($dataAndataPrenotata < $dataMinima || $dataAndataPrenotata > $dataMassima) {
        header("location: home.php?error=data_non_valida");
        exit;
    }
    
    // --- CALCOLO ORA RIENTRO ---

    $dataClone = new DateTime($andataString);
    
    $ore = (int)$_POST['oreDurata'];
    $minuti = (int)$_POST['minutiDurata'];
    
    // --- RIENTRO ---
    
    $rientro = clone $dataClone;
    $rientro->modify("+$ore hours");
    $rientro->modify("+$minuti minutes");
    $rientroString = $rientro->format('Y-m-d H:i:s');



    // 1. Prima Insert (Andata)
    $sqlA = "INSERT INTO missione (id_utente, id_obiettivo, id_destinazione, data, durata, statoCompilazione,tipo) 
             VALUES ('$idUtente', 1, '$idDestinazione', '$andataString', '$durata', '$statoCompilazione','ANDATA')";


    if(mysqli_query($db, $sqlA)){
        // 2. Seconda Insert (Ritorno)
        $id_andata = mysqli_insert_id($db);

        $sqlR = "INSERT INTO missione (id_utente, id_obiettivo, id_destinazione, data, durata, statoCompilazione,tipo,id_collegamento) 
             VALUES ('$idUtente', '$idDestinazione', 1, '$rientroString', '00:00:00', '$statoCompilazione','RITORNO','$id_andata')";
        
        if(mysqli_query($db, $sqlR)){
            $id_ritorno = mysqli_insert_id($db);

            // 3. AGGIORNAMENTO: Inseriamo l'ID del ritorno nella missione di andata
            $sqlUpdateAndata = "UPDATE missione SET id_collegamento = '$id_ritorno' WHERE ID = '$id_andata'";
            mysqli_query($db, $sqlUpdateAndata);

            mysqli_close($db);
            header("location: home.php?success=1");
            exit;
        }

        mysqli_close($db);
        header("location: home.php?error=db_error");
        exit;

    } else {
        mysqli_close($db);
        header("location: home.php?error=db_error");
        exit;
    }
?>