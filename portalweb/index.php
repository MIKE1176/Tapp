<?php
  include("session.php");
  check_auth(); // Se non loggato o non attivo, scappa e va al login
?>

<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>
  <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
  <title>Tapp - Home</title>
</head>

<body>
  <?php
  include('./navbar.php');
  $idOperatore = $_SESSION['ID'];

  $queryNMissioni = "SELECT missione.* FROM missione JOIN turno ON missione.id_turno = turno.ID WHERE turno.id_operatore = $idOperatore AND DATE(missione.data)=CURDATE()";

  $queryNTurno = "SELECT * FROM turno WHERE id_operatore = $idOperatore AND (DATE(dataInizio)=CURDATE() OR DATE(dataInizio)=DATE_ADD(CURDATE(),INTERVAL 1 DAY))";  
  $nMissioni = mysqli_query($db, $queryNMissioni);

  $nTurni = mysqli_query($db, $queryNTurno);
  
  ?>

  <?php
  if (isset($_SESSION['errore'])) {
    if($_SESSION['errore'] == "passwordDiverse") {
      echo "<script>alert('La conferma della nuova password non corrisponde! Modifica non effettuata.');</script>";
      unset($_SESSION['errore']);
    }elseif ($_SESSION['errore'] == "passwordVecchia") {
      echo "<script>alert('Autenticazione fallita. Modifica non effettuata.');</script>";
      unset($_SESSION['errore']);
    }elseif ($_SESSION['errore'] == "passwordUgualeVecchia") {
      echo "<script>alert('La password che hai inserito è uguale a quella vecchia. Modifica non effettuata');</script>";
      unset($_SESSION['errore']);
    }
  }
  ?>

  <div class="d-block text-center">
    <div class="container">
      <div class="row align-text-center mt-2">
        <div class="col">
          <h1>Benvenuto, <?php  echo $_SESSION['nome']?>!</h1>
        </div>
      </div>
      <div class="row align-content-center mt-2">
        <div class="col-6 col-xl-3 mx-auto">
          <img src="../assets/icons/logo.jpg" class="img-fluid">
        </div>
      </div>
      <div class="row justify-content-center mt-2">
        <div class="col-12 col-md-6 mb-4">
          <div class="card p-3 shadow">
            <h2>I tuoi turni</h2>
            <p>Hai <?php echo mysqli_num_rows($nTurni); ?> turni programmati tra oggi e domani.</p>
            <a href="mieiTurni.php" class="btn btn-light">Vai alla pagina</a>
          </div>
        </div>
        <div class="col-12 col-md-6 mb-4">
          <div class="card p-3 shadow">
            <h2>I tuoi servizi</h2>
            <p>Per la giornata di oggi hai assegnate <?php echo mysqli_num_rows($nMissioni); ?> trasporti.</p>
            <a href="missioni.php" class="btn btn-light">Vai alla pagina servizi</a>
          </div>
        </div>
      </div>
      <div class="row justify-content-center">
        <button class="btn btn-outline-danger w-50 h-100 my-3" data-bs-toggle="modal" data-bs-target="#modificaPassword">
          <span class="fw-bold text-center">MODIFICA PASSWORD</span>
        </button>
      </div>
    </div>
  </div>

  <!-- modal MODIFICA PASSWORD -->
  <div class="modal fade" id="modificaPassword" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Modifica Password</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="./modificaPassword.php" method="post">
          <div class="modal-body">
            <div class="mb-3"><!-- VECCHIA PASSWORD -->
              <label for="vecchiaPassword">Vecchia Password</label> <br>
              <input type="password" name="vecchiaPassword" class="w-100 form-control" required>
            </div>
            <div class="mb-3"><!-- NUOVA PASSWORD -->
              <label for="nuovaPassword">Nuova Password</label> <br>
              <input type="password" id="nuovaPassword" name="nuovaPassword" class="w-100 form-control" required>
            </div>

            <div class="mb-3"><!-- CONFERMA PASSWORD -->
              <label for="confermaPassword">Conferma Password</label> <br>
              <input type="password" id="confermaPassword" name="confermaPassword" class="w-100 form-control" oninput="verificaPassword()" required>
              <div id="message" class="text-danger"></div>
            </div>

            <script>
                function verificaPassword() {
                    var nuovaPassword = document.getElementById("nuovaPassword").value;
                    var confermaPassword = document.getElementById("confermaPassword").value;

                    // Verifica se le password corrispondono
                    if (nuovaPassword === confermaPassword) {
                        document.getElementById("message").innerHTML = "";
                    } else {
                        document.getElementById("message").innerHTML = "Le password non corrispondono!";
                    }
                }
            </script>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Annulla</button>
            <button type="submit" class="btn btn-success">Modifica</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  
</body>

</html>