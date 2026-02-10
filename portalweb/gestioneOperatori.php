<?php
  include("session.php");
  check_auth();

  if($_SESSION['auth']!="AMMINISTRATIVO"){
    header("location: index.php");
    exit();
  }
  //query che mi ricerca tutti i operatori
  $queryOperatori = "SELECT * FROM operatore order by operatore.cognome asc";
  $operatori = mysqli_query($db, $queryOperatori);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
  <title>Tapp - Gestione operatori</title>
</head>

<body>
  <?php
    include('./navbar.php');
  ?>
  <script>
      document.addEventListener("DOMContentLoaded", () => {
          const navLinks = document.querySelectorAll(".nav-link");
          navLinks.forEach(link => link.classList.remove("fw-bold"));
          const currentNav = document.getElementById("gestioneOperatori");
          if(currentNav) currentNav.classList.add("fw-bold");
      });
  </script>
  <?php
    if (isset($_SESSION['errore'])) {
        $msg = ($_SESSION['errore'] == "username") ? "Username già in uso. Operatore non creato." : "Username già esistente. Modifica annullata.";
        echo "<script>alert('$msg');</script>";
        unset($_SESSION['errore']);
    }
  ?>

  <!-- VISUALIZZA operatori DISATTIVATI -->
  <div class="container mt-4">
    <div class="row justify-content-center mb-3">
        <div class="col-12 col-md-6 text-center">
            <form id="formToggle" action="" method="POST" class="form-check form-switch d-inline-block">
                <?php 
                  $visualizzaDisattivati = $_POST['operatoriDisattivatiN'] ?? 0; 
                  $checked = ($visualizzaDisattivati == 1) ? "checked" : "";
                ?>
                <input class="d-none" type="number" value="<?php echo $visualizzaDisattivati; ?>" name="operatoriDisattivatiN" id="operatoriN">
                <input class="form-check-input" type="checkbox" id="flexCheckDefault" onchange="updateValue()" <?php echo $checked; ?>>
                <label class="form-check-label" for="flexCheckDefault">Visualizza operatori disattivati</label>
            </form>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-md-6">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0">🔍</span>
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cerca per nome o cognome...">
            </div>
        </div>
    </div>
  </div>

  <!-- LISTA OPERATORI -->

  <div class="container mt-4">
    <div class="row" id="operatorContainer">
      <?php
      //cerco i operatori
      if (mysqli_num_rows($operatori) != 0) {
        while ($row = mysqli_fetch_assoc($operatori)) {        //CON mysql_fetch_assoc($operatori) ottengo il risultato riga per riga dei operatori


          //-------------------------  DATI operatori  -------------------------

          $id = $row['ID'];
          $cognome = $row['cognome'];
          $nome = $row['nome'];
          $sesso = $row['sesso'];
          $dataNascita = $row['dataNascita'];
          $telefono = $row['telefono'];
          $username = $row['username'];
          $responsabile = $row['utente'];
          $attivo = $row['attivo'];

          if (!($visualizzaDisattivati == 0 and !$attivo)) {
              $row_class = (!$attivo) ? 'border border-3 border-danger bg-light' : 'shadow';
      ?>

        <div class="col-12 col-md-6 col-lg-4 py-3 operator-card" data-name="<?php echo strtolower($cognome . ' ' . $nome); ?>">
          <div class="card p-3 h-100 <?php echo $row_class ?>">
            <div class="card-body">
              <h3 class="fw-bold text-center mb-3">
                <?php echo $cognome . ' ' . $nome; ?>
              </h3>
              <div class="mb-3 text-center">
                  <span class="badge bg-primary"><?php echo $responsabile; ?></span>
              </div>
              
              <p class="mb-1 text-muted">Telefono:</p>
              <h4 class="mb-3"><?php echo $telefono; ?></h4>

              <div class="row g-2">
                <div class="col-4">
                  <button class="btn btn-info text-white w-100" data-bs-toggle="modal" data-bs-target="#ModalInfoOperatore<?php echo $id; ?>">Dettagli</button>
                </div>
                <div class="col-4">
                  <button class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#ModalModificaOperatore<?php echo $id; ?>">Modifica</button>
                </div>
                <div class="col-4">
                  <button class="btn btn-<?php echo (!$attivo) ? "success" : "outline-danger"; ?> w-100" data-bs-toggle="modal" data-bs-target="#ModalDisattivaOperatore<?php echo $id; ?>">
                      <?php echo (!$attivo) ? "Attiva" : "Disattiva"; ?>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="ModalInfoOperatore<?php echo $id; ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">Dettagli Operatore</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Nominativo:</strong> <?php echo $nome . " " . $cognome; ?></li>
                            <li class="list-group-item"><strong>Sesso:</strong> <?php echo ($sesso == 'M') ? 'Maschio' : 'Femmina'; ?></li>
                            <li class="list-group-item"><strong>Data di Nascita:</strong> <?php echo date("d/m/Y", strtotime($dataNascita)); ?></li>
                            <li class="list-group-item"><strong>Telefono:</strong> <?php echo $telefono; ?></li>
                            <li class="list-group-item"><strong>Qualifica:</strong> <?php echo $responsabile; ?></li>
                            <li class="list-group-item"><strong>Username:</strong> <?php echo $username; ?></li>
                            <li class="list-group-item"><strong>Stato Account:</strong> <?php echo ($attivo) ? '<span class="text-success">Attivo</span>' : '<span class="text-danger">Disattivato</span>'; ?></li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="<?php echo 'ModalModificaOperatore' . $id; ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h1 class="modal-title fs-5">Modifica Operatore</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <form action="./modificaOperatore.php" method="post">
                    <input type="hidden" name="idOperatore" value="<?php echo $id; ?>">
                    <div class="modal-body">
                      <div class="mb-3">
                        <div class="input-group">
                          <span class="input-group-text">Nome e cognome</span>
                          <input type="text" value="<?php echo $nome; ?>" name="nome" class="form-control" required>
                          <input type="text" value="<?php echo $cognome; ?>" name="cognome" class="form-control" required>
                        </div>
                      </div>
                      <div class="mb-3">
                        <label>Data di nascita</label>
                        <input type="date" name="dataNascita" value="<?php echo $dataNascita; ?>" class="form-control" required>
                      </div>
                      <div class="mb-3">
                        <label>Sesso</label>
                        <select class="form-select" name="sesso">
                          <option value="M"<?php if ($sesso == "M") echo " selected"; ?>>Maschio</option>
                          <option value="F"<?php if ($sesso == "F") echo " selected"; ?>>Femmina</option>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label>Telefono</label>
                        <input type="tel" name="telefono" value="<?php echo $telefono; ?>" class="form-control" required>
                      </div>
                      <div class="mb-3">
                          <label>Qualifica</label>
                          <select class="form-select" name="responsabile">
                              <option value="AUTISTA"<?php if ($responsabile == "AUTISTA") echo " selected"; ?>>Autista</option>
                              <option value="AMMINISTRATIVO"<?php if ($responsabile == "AMMINISTRATIVO") echo " selected"; ?>>Amministrativo</option>
                          </select>
                      </div>
                      <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" value="<?php echo $username; ?>" class="form-control" required>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Annulla</button>
                      <button type="submit" class="btn btn-success">Salva</button>
                    </div>
                  </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="<?php echo 'ModalDisattivaOperatore' . $id; ?>" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5"><?php echo (!$attivo) ? "Attivazione" : "Disattivazione"; ?></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                Sei sicuro di voler <?php echo (!$attivo) ? "riattivare" : "disattivare"; ?> <?php echo $nome . ' ' . $cognome; ?>?
              </div>
              <div class="modal-footer">
                  <form action="./toggleOperatore.php" method="post">
                    <input type="hidden" name="idOperatore" value="<?php echo $id; ?>">
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Annulla</button>
                    <button class="btn btn-danger" type="submit"><?php echo (!$attivo) ? "Attiva" : "Disattiva"; ?></button>
                  </form>
              </div>
            </div>
          </div>
        </div>
      <?php
          }
        }
      }else {
        echo "<div class='col-12 text-center'>Nessun Operatore trovato.</div>";
      }
      ?>

      <!-- BOTTONE + -->
      <div class="col-12 col-md-6 col-lg-4 py-3">
        <button type="button" class="btn btn-outline-success w-100 h-100" data-bs-toggle="modal" data-bs-target="#aggiungiOperatore">
          <h1 class="fs-1">+</h1>
        </button>
      </div>
    </div>
  </div>

  <!-- Modal BOTTONE + -->
  <div class="modal fade" id="aggiungiOperatore" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Aggiungi Operatore</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="./aggiungiOperatore.php" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome e cognome</label>
                        <div class="input-group">
                            <input type="text" placeholder="Mario" maxlength="30" name="nome" class="form-control" pattern="[A-Za-z\s]+" required>
                            <input type="text" placeholder="Rossi" maxlength="30" name="cognome" class="form-control" pattern="[A-Za-z\s]+" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Data di nascita</label>
                            <input type="date" name="dataNascita" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sesso</label>
                            <select class="form-select" name="sesso">
                                <option value="M">Maschio</option>
                                <option value="F">Femmina</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Numero di telefono</label>
                        <input type="tel" name="telefono" maxlength="10" class="form-control" pattern="[0-9]+" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Qualifica</label>
                        <select class="form-select" name="responsabile">
                            <option value="AUTISTA">Autista</option>
                            <option value="AMMINISTRATIVO">Amministrativo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-success">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>

  <script>
    function updateValue() {
        let inputN = document.getElementById("operatoriN");
        inputN.value = (inputN.value == "0") ? "1" : "0";
        document.getElementById("formToggle").submit();
    }

    // RICERCA DINAMICA
    document.getElementById('searchInput').addEventListener('input', function() {
        let filter = this.value.toLowerCase().trim();
        let cards = document.querySelectorAll('.operator-card');
        
        cards.forEach(card => {
            let name = card.getAttribute('data-name');
            if (name.includes(filter)) {
                card.style.setProperty('display', '', 'important');
            } else {
                card.style.setProperty('display', 'none', 'important');
            }
        });
    });
  </script>
</body>

</html>