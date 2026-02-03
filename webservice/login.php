<?php
include("./config.php"); 
session_name("webservice");
session_start();

if(isset($_SESSION['id']) && isset($_SESSION['sessione']) && $_SESSION['sessione'] === 'webservice'){ // Verifica se l'utente ha già effettuato l'accesso, se sì, reindirizza alla pagina di benvenuto
    header('Location: home.php');
    exit;
}

// 1. Recupero i dati dal POST
$myusername = $_POST['username'] ?? '';
$mypassword = $_POST['password'] ?? '';

// 2. Unica query sicura per prendere tutto l'utente
$query = $db->prepare("SELECT ID, nome, cognome, username, password FROM utente WHERE username = ?");
$query->bind_param('s', $myusername);
$query->execute();
$result = $query->get_result();

// 3. Controllo se l'utente esiste
if ($row = $result->fetch_assoc()) {
    
    // 4. Verifico la password hashata
    if (password_verify($mypassword, $row['password'])) {
        
        // LOGIN RIUSCITO: Salvo tutto in sessione
        $_SESSION['id'] = $row['ID'];
        $_SESSION['nome'] = $row['nome'];
        $_SESSION['cognome'] = $row['cognome'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['sessione'] = 'webservice';
        
        header("Location: home.php");
        exit;

    } else {
        // Password sbagliata
        $_SESSION['errore'] = "credenzialiSbagliate";
        header("Location: home.php");
        exit;
    }
} else {
    // Username non trovato
    $_SESSION['errore'] = "credenzialiSbagliate";
    header("Location: home.php");
    exit;
}

mysqli_close($db);
?>