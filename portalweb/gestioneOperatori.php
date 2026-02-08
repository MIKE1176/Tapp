<?php
  include("session.php");
  check_auth();

  if($_SESSION['auth']!="AMMINISTRATIVO"){
    header("location: index.php");
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
  <title>TAPP - Gestione operatori</title>
</head>

<body>
  <?php
    include('./navbar.php');
  ?>
  <script>
      [].slice.call(document.getElementsByClassName("nav-link")).forEach(element => {
          element.classList.remove("fw-bold");
      });
      document.getElementById("gestioneOperatori").classList.add("fw-bold");
  </script>
  <?php
    if (isset($_SESSION['errore'])) {
        if ($_SESSION['errore'] == "username") {
            echo "<script>alert('Username dell\'operatore già inserito. Operatore non creato.');</script>";
            unset($_SESSION['errore']);
        }elseif ($_SESSION['errore'] == "mOperatore") {
            echo "<script>alert('Username dell\'operatore che stai modificando è gia inserito. Operatore non modificato.');</script>";
            unset($_SESSION['errore']);
        }
    }
  ?>

  <!-- VISUALIZZA operatori DISATTIVATI -->
  <?php
      $visualizzaDisattivati=0;
      $valoreRisultato="";
      if(isset($_POST["operatoriDisattivatiN"])){
          $visualizzaDisattivati = $_POST['operatoriDisattivatiN'];
          if($visualizzaDisattivati == 1){
              $valoreRisultato = "checked";
          }else{
              $valoreRisultato = "";
          }
      }
  ?>

  <div class="container">
      <div class="row justify-content-center align-items-center mt-3">
          <div class="align-items-center">
              <form class="form-check form-switch text-center" id="formToggle" action="" method="POST">
                  <div class="form-check form-check-inline">
                      <input class="d-none" type="number" value="<?php echo $visualizzaDisattivati;?>" name="operatoriDisattivatiN" id="operatoriN">
                      <input class="form-check-input" type="checkbox" name="operatoriDisattivati" id="flexCheckDefault" onchange="updateValue()" <?php  echo $valoreRisultato;?>>
                      <label class="form-check-label" for="flexCheckDefault">Visualizza operatori disattivati</label>
                  </div>
              </form>
          </div>
      </div>
  </div>

  <script>
      function updateValue() {
          if(document.getElementById("operatoriN").value == "0"){
              document.getElementById("operatoriN").value = "1";
          }else{
              document.getElementById("operatoriN").value = "0";
          }
          document.getElementById("formToggle").submit();
      }
  </script>

  <!-- LISTA OPERATORI -->

  <div class="container vh-100">
    <div class="row">
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
              $row_class = "";
              if ($visualizzaDisattivati == 1 and !$attivo) {
                  $row_class = 'border border-3 border-danger';
              }

      ?>

          <div class="col-12 col-md-6 col-lg-4 py-3">
            <div class="card p-3 shadow <?php echo $row_class ?>">
              <div class="col d-flex align-items-center">
                <div class="row p-1 justify-content-center">
                  <h3 class="fw-bold text-center">
                    <?php echo $cognome . ' ' . $nome . ' (' . $sesso . ')'; ?>
                  </h3>
                  <div>
                    <div class="row p-1">
                      <span class="fs-5">Data di nascita</span>
                      <span class="fs-2">
                        <?php echo $dataNascita; ?>
                      </span>
                    </div>
                    <div class="row p-1">
                      <span class="fs-5">Telefono</span>
                      <span class="fs-2">
                        <?php echo $telefono; ?>
                      </span>
                    </div>
                    <div class="row p-2 justify-content-center">
                      <div class="col-6">
                        <!-- bottone MODIFICA -->
                        <button type="submit" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#<?php echo 'ModalModificaOperatore' . $id; ?>">
                          Modifica
                        </button>
                      </div>
                      <!-- bottone DISATTIVA -->
                      <div class="col-6">
                        <button type="submit" class="btn btn-<?php
                          if (!$attivo) {
                              echo "success";
                          } else {
                              echo "outline-danger";
                          }
                          ?> w-100" data-bs-toggle="modal" data-bs-target="#<?php echo 'ModalDisattivaOperatore' . $id; ?>">
                            <?php
                            if (!$attivo) {
                                echo "Attiva";
                            } else {
                                echo "Disattiva";
                            }
                            ?>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal MODIFICA -->
          <div class="modal fade" id="<?php echo 'ModalModificaOperatore' . $id; ?>" tabindex="-1">
              <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h1 class="modal-title fs-5" id="exampleModalLabel">Modifica Operatore </h1>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="./modificaOperatore.php" method="post">
                      <input type="text" name="idOperatore" value="<?php echo $id; ?>" class="d-none"></input>
                      <div class="modal-body">
                        <div class="mb-3">
                          <div class="input-group">
                            <span class="input-group-text">Nome e cognome</span>
                            <input type="text" placeholder="Mario" maxlength="30" value="<?php echo $nome; ?>" name="nome" class="form-control" pattern="[A-Za-z]+" required>
                            <input type="text" placeholder="Rossi" maxlength="30" value="<?php echo $cognome; ?>"name="cognome" class="form-control" pattern="[A-Za-z]+" required>
                          </div>
                        </div>
                        <div class="mb-3">
                          <label for="dataNascita">Data di nascita</label> <br>
                          <input type="date" name="dataNascita" value="<?php echo $dataNascita; ?>" class="w-100 form-control" required>
                        </div>
                        <div class="mb-3">
                          <label for="floatingInput">Sesso</label> <br>  <!-- Controllo quello che è selezionato con gli if -->
                          <select class="form-select" name="sesso" value="<?php echo $sesso; ?>">
                            <option value="M"<?php if ($sesso == "M") echo " selected"; ?>>Maschio</option>
                            <option value="F"<?php if ($sesso == "F") echo " selected"; ?>>Femmina</option>
                          </select>
                        </div>
                        <div class="mb-3">
                          <label for="codiceMezzo">Numero di telefono</label> <br>
                          <input type="tel" name="telefono" maxlength="10" value="<?php echo $telefono; ?>" class="w-100 form-control" pattern="[0-9]+" required>
                        </div>
                        <div class="mb-3">
                            <label for="floatingInput">Qualifica</label> <br> <!-- Controllo quello che è selezionato con gli if -->
                            <select class="form-select" name="responsabile">
                                <option value="AUTISTA"<?php if ($responsabile == "AUTISTA") echo " selected"; ?>>Autista</option>
                                <option value="AMMINISTRATIVO"<?php if ($responsabile == "AMMINISTRATIVO") echo " selected"; ?>>Amministrativo</option>
                            </select>
                        </div>
                        <div class="mb-3">
                          <label for="username">Username</label> <br>
                          <input type="text" name="username" value="<?php echo $username; ?>" class="w-100 form-control" required>
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
          <!-- MODAL DISATTIVA -->
          <div class="modal fade" id="<?php echo 'ModalDisattivaOperatore' . $id; ?>" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="exampleModalLabel">
                    <?php
                        if (!$attivo) {
                            echo "Attivazione Operatore";
                        } else {
                            echo "Disattivazione Operatore";
                        }
                    ?>
                  </h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  Sei sicuro di voler 
                  <?php
                    if (!$attivo) {
                        echo "riattivare";
                    } else {
                        echo "disattivare";
                    }
                  ?>
                  l'Operatore <?php echo $nome . ' ' . $cognome; ?>?
                </div>
                <div class="modal-footer">
                  <div class="d-grid gap-2 mx-auto">
                    <form action="./toggleOperatore.php" method="post">
                      <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Annulla</button>
                      <input type="text" name="idOperatore" value="<?php echo $id; ?>" class="d-none"></input>
                      <button class="btn btn-danger" type="submit" id="bottone">
                      <?php
                        if (!$attivo) {
                            echo "Attiva";
                        } else {
                            echo "Disattiva";
                        }
                      ?>
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
      <?php
          }
        }
      }else {
          echo "Nessun Operatore trovato.";
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
              <h1 class="modal-title fs-5" id="exampleModalLabel">Aggiungi Operatore</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="./aggiungiOperatore.php" method="post">
              <div class="modal-body">
                <div class="mb-3">
                  <div class="input-group">
                    <span class="input-group-text">Nome e cognome</span>
                    <input type="text" placeholder="Mario" maxlength="30" name="nome" class="form-control" pattern="[A-Za-z]+" required>
                    <input type="text" placeholder="Rossi" maxlength="30" name="cognome" class="form-control" pattern="[A-Za-z]+" required>
                  </div>
                </div>
                <div class="mb-3">
                  <label for="dataNascita">Data di nascita</label> <br>
                  <input type="date" name="dataNascita" class="w-100 form-control" required>
                </div>
                <div class="mb-3">
                  <label for="floatingInput">Sesso</label> <br>
                  <select class="form-select" name="sesso">
                    <option value="M">Maschio</option>
                    <option value="F">Femmina</option>
                  </select>
                </div>
                <div class="col-6">
                  <label for="codiceMezzo">Numero di telefono</label> <br>
                  <input type="tel" name="telefono" maxlength="10" class="w-100 form-control" pattern="[0-9]+" required>
                </div>
                <div class="mb-3">
                  <label for="floatingInput">Qualifica</label> <br>
                  <select class="form-select" name="responsabile">
                    <option value="AUTISTA">Autista</option>
                    <option value="AMMINISTRATIVO">Amministrativo</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="username">Username</label> <br>
                  <input type="text" name="username" class="w-100 form-control" required>
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
</body>

</html>