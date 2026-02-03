<?php
  include("./config.php");  //file per la connessione al DB
  session_name("portalweb");
  session_start();
  if(isset($_SESSION['username']) && isset($_SESSION['sessione']) && $_SESSION['sessione'] === 'portalweb'){ // Verifica se l'utente ha già effettuato l'accesso, se sì, reindirizza alla pagina di benvenuto
    header('Location: index.php');
    exit;
  }
?>

<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
  <title>Accesso - Tapp PortalWeb</title>
</head>

<body class="bg-info">
  <div class="container h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card bg-dark rounded-3">
          <div class="card-body px-5 pt-4 pb-5 text-center">
            <img src="../assets/icons/logo.jpg" class="rounded mx-auto d-block w-50 h-50 mb-3 mt-2" alt="posto di blocco, arrestarsi">
            <p class="text-white mb-5" id="text">Inserisci le tue credenziali per accedere al portale</p>
            <?php
              if ($_SERVER["REQUEST_METHOD"] == "POST") {
                  $myusername = $_POST['username'] ?? '';
                  $mypassword = $_POST['password'] ?? '';

                  // 1. Usiamo i Prepared Statements per evitare SQL Injection
                  // Prendiamo sia la password che lo stato 'attivo' in una sola volta
                  $query = $db->prepare("SELECT password, attivo FROM operatore WHERE username = ?");
                  $query->bind_param('s', $myusername);
                  $query->execute();
                  $result = $query->get_result();

                  if ($row = $result->fetch_assoc()) {
                      // L'utente esiste, ora controlliamo se è attivo
                      if ($row['attivo'] == 0) {
                          echo '<p class="text-danger mb-5">Il tuo accesso al portale Tapp è scaduto.</p>';
                      } 
                      // Se è attivo, verifichiamo la password
                      else if (password_verify($mypassword, $row['password'])) {
                          // LOGIN OK
                          $_SESSION['username'] = $myusername;
                          $_SESSION['sessione'] = 'portalweb';
                          header("Location: index.php");
                          exit;
                      } 
                      // Password errata
                      else {
                          echo '<p class="text-danger mb-5">Le credenziali che hai inserito non sono corrette, riprova.</p>';
                      }
                  } else {
                      // L'utente non esiste nemmeno nel database
                      echo '<p class="text-danger mb-5">Utente inesistente o credenziali errate.</p>';
                  }
                  
                  $query->close();
              }
              mysqli_close($db);
            ?>
            <form action="" method="post">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="username" placeholder="mikecami" required>
                <label for="floatingInput">Utente</label>
              </div>
              <div class="form-floating mb-3">
                <input type="password" class="form-control" name="password" placeholder="mikecami" required>
                <label for="floatingPassword">Password</label>
              </div>
              <button class="btn btn-outline-light btn-lg px-5" type="submit">Accedi</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>