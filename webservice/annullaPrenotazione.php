<?php
    include("./session.php");
    check_auth();

    if (isset($_POST['idPrenotazione'])) {
        $id = mysqli_real_escape_string($db, $_POST['idPrenotazione']);
        $idUtente = $_SESSION['ID'];

        // Questa query annulla:
        // 1. La missione stessa (Andata)
        // 2. La missione di ritorno collegata (tramite id_collegamento)
        $sql = "UPDATE missione 
            SET statoCompilazione = 'ANNULLATA' 
            WHERE (ID = '$id' OR id_collegamento = '$id') 
            AND id_utente = '$idUtente'
            AND statoCompilazione != 'COMPLETATA'";

        if (mysqli_query($db, $sql)) {
            $righe_modificate = mysqli_affected_rows($db);
            if ($righe_modificate > 0) {
                header("location: home.php?success=annullata&count=$righe_modificate");
            } else {
                // La query è giusta ma non ha trovato l'ID nel DB
                header("location: home.php?error=nessuna_riga_trovata");
            }
        }
    }
?>