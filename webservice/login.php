<?php
    include("./session.php");
    check_auth(true); // Se sono già loggato, mi manda a home.php


    // 0. Eseguiamo la logica di login SOLO se il form è stato inviato
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'])) {
        // 1. Recupero i dati dal POST
        $myusername = $_POST['username'] ?? '';
        $mypassword = $_POST['password'] ?? '';

        // 2. Unica query sicura per prendere tutto l'utente
        $query = $db->prepare("SELECT ID, password FROM utente WHERE username = ?");
        $query->bind_param('s', $myusername);
        $query->execute();
        $result = $query->get_result();

        // 3. Controllo se l'utente esiste
        if ($row = $result->fetch_assoc()) {
            
            // 4. Verifico la password hashata
            if (password_verify($mypassword, $row['password'])) {
                
                session_regenerate_id(true); // Per sicurezza extra
                // LOGIN RIUSCITO: Salvo tutto in sessione
                $_SESSION['ID'] = $row['ID'];
                $_SESSION['username'] = $myusername;
                $_SESSION['session'] = 'webservice';
                
                session_write_close();

                header("Location: home.php");
                exit;

            } else {
                // Password sbagliata
                $_SESSION['errore'] = "credenzialiSbagliate";
                header("Location: accedi.php");
                exit;
            }
        } else {
            // Username non trovato
            $_SESSION['errore'] = "credenzialiSbagliate";
            header("Location: accedi.php");
            exit;
        }
        $query->close();
    }

    mysqli_close($db);
?>