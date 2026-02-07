<?php
  include("session.php");
  check_auth(); // Se non loggato o non attivo, scappa e va al login

  if($_SESSION['auth']!="AMMINISTRATIVO"){
    header("location: index.php");
  }

  //query che mi ricerca tutti i luoghi
  $queryLuoghi = "SELECT luogo.ID,luogo.denominazione,luogo.indirizzo,luogo.civico,luogo.citta,luogo.note,luogo.attivo FROM luogo WHERE ID != 1 and ID != 2 order by luogo.denominazione asc";
  $luoghi = mysqli_query($db, $queryLuoghi);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>
  <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
  <title>Tapp - Gestione Luoghi</title>
</head>

<body>
  <?php
  include('./navbar.php');
  ?>
  <script>
      [].slice.call(document.getElementsByClassName("nav-link")).forEach(element => {
          element.classList.remove("fw-bold");
      });
      document.getElementById("gestioneLuoghi").classList.add("fw-bold");
  </script>
  <?php
    if (isset($_SESSION['errore'])) {
        if ($_SESSION['errore'] == "aLuogo") {
            echo "<script>alert('La denominazione del luogo che hai provato a inserire è già presente nel database. Luogo non creato.');</script>";
            unset($_SESSION['errore']);
        }elseif ($_SESSION['errore'] == "mLuogo") {
            echo "<script>alert('La denominazione del luogo che hai provato a modificare è già presente nel database. Luogo non modificato.');</script>";
            unset($_SESSION['errore']);
        }
    }
  ?>

  <!-- VISUALIZZA LUOGHI DISATTIVATI -->
  <?php
  $visualizzaDisattivati = 0;
  $valoreRisultato = "";
  if (isset($_POST["luoghiDisattivatiN"])) {
    $visualizzaDisattivati = $_POST['luoghiDisattivatiN'];
    if ($visualizzaDisattivati == 1) {
      $valoreRisultato = "checked";
    } else {
      $valoreRisultato = "";
    }
  }
  ?>

  <div class="container">
    <div class="row justify-content-center align-items-center mt-3">
      <div class="align-items-center">
        <form class="form-check form-switch text-center" id="formToggle" action="" method="POST">
          <div class="form-check form-check-inline">
            <input class="d-none" type="number" value="<?php echo $visualizzaDisattivati; ?>" name="luoghiDisattivatiN"
              id="luoghiN">
            <input class="form-check-input" type="checkbox" name="luoghiDisattivati" id="flexCheckDefault"
              onchange="updateValue()" <?php echo $valoreRisultato; ?>>
            <label class="form-check-label" for="flexCheckDefault">Visualizza luoghi disattivati</label>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    function updateValue() {
      if (document.getElementById("luoghiN").value == "0") {
        document.getElementById("luoghiN").value = "1";
      } else {
        document.getElementById("luoghiN").value = "0";
      }
      document.getElementById("formToggle").submit();
    }
  </script>

  <!-- LISTA LUOGHI -->

  <div class="container vh-100">
    <div class="row">
      <?php 
        //cerco i luoghi
        if (mysqli_num_rows($luoghi) != 0) {
          while ($row = mysqli_fetch_assoc($luoghi)) {        //CON mysql_fetch_assoc($luoghi) ottengo il risultato riga per riga dei luoghi
      

              //-------------------------  DATI LUOGHI  -------------------------

              $id = $row['ID'];
              $denominazione = $row['denominazione'];
              $indirizzo = $row['indirizzo'];
              $civico = $row['civico'];
              $citta = $row['citta'];
              $note = $row['note'];
              $attivo = $row['attivo'];

              if (!($visualizzaDisattivati == 0 and !$attivo)) {
                  $row_class = "";
                  if ($visualizzaDisattivati == 1 and !$attivo) {
                      $row_class = 'border border-3 border-danger';
                  }
          
      ?>
      <div class="col-12 col-md-6 col-lg-4 py-3">
        <div class="card p-3 shadow <?php echo $row_class ?>">
          <div class="col ">
            <div class="row p-1 justify-content-center">
              <span class="fs-3 fw-bold text-center">
                <?php echo $denominazione; ?>
              </span>

            </div>
            <div class="row p-1">
              <span class="fs-5 text-center">
                <?php echo $indirizzo . ', ' . $civico . ' - ' . $citta; ?>
              </span>
            </div>

            <!-- TENDINA NOTE-->
            <div class="row p-1">
              <div class="accordion" id="<?php echo $id; ?>_2">
                <div class="accordion-item">
                  <span class="fs-2 accordion-header">
                    <button class="accordion-button bg-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo 'collapse' . $id; ?>_2" aria-expanded="false">
                      Note
                    </button>
                  </span>
                  <div id="<?php echo 'collapse' . $id; ?>_2" class="accordion-collapse collapse" data-bs-parent="#<?php echo $id; ?>_2">
                    <div class="accordion-body">
                      <?php echo $note; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row p-2 justify-content-center">
              <div class="col-6">
                <button type="submit" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#<?php echo 'ModalModificaLuogo' . $id; ?>">
                  Modifica
                </button>
              </div>
              <div class="col-6">
                <button type="submit" class="btn btn-<?php
                  if (!$attivo) {
                      echo "success";
                  } else {
                      echo "outline-danger";
                  }
                  ?> w-100" data-bs-toggle="modal" data-bs-target="#<?php echo 'ModalDisattivaLuogo' . $id; ?>">
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

      <!-- MODAL MODIFICA -->
      <div class="modal fade" id="<?php echo 'ModalModificaLuogo' . $id; ?>" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="modificaLuogo">
                MODIFICA
                <?php
                  echo '"'.$denominazione.'"';
                ?>
              </h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form action="./modificaLuogo.php" method="post">
                <input type="text" name="idLuogo" value="<?php echo $id; ?>" class="d-none"></input>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="denominazione">Denominazione</label> <br>
                        <input type="text" id="denominazione" class="w-100 form-control" maxlength="30" name="denominazione" value="<?php echo $denominazione;?>" required>
                    </div>
                    <div class="row mb-3">
                      <div class="col-9">
                        <label for="indirizzo">Indirizzo</label> <br>
                        <input type="text" name="indirizzo" class="w-100 form-control" maxlength="30" value="<?php echo $indirizzo;?>" required>
                      </div>
                      <div class="col-3">
                        <label for="civico">Civico</label> <br>
                        <input type="text" name="civico" class="w-100 form-control" maxlength="5" value="<?php echo $civico;?>" required>
                      </div>
                    </div>
                    <div class="mb-3">
                        <label for="citta">Citta</label> <br>
                        <input type="text" name="citta" class="w-100 form-control" maxlength="30" value="<?php echo $citta;?>" required>
                    </div>
                    <div class="form-floating">
                      <textarea class="form-control"  name="note" class="w-100 form-control" id="noteBlock" style="height: 100px"><?php echo $note;?></textarea>
                      <label for="noteBlock">Note</label>
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
      </div>

      <!-- MODAL DISATTIVA/ATTIVA -->
      <div class="modal fade" id="<?php echo 'ModalDisattivaLuogo' . $id; ?>" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="disattivaLuogo">
                <?php
                    if (!$attivo) {
                        echo "Attivazione luogo";
                    } else {
                        echo "Disattivazione luogo";
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
              il luogo <?php echo $denominazione; ?>?
            </div>
            <div class="modal-footer">
              <div class="d-grid gap-2 mx-auto">
                <form action="./toggleLuogo.php" method="post">
                  <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Annulla</button>
                  <input type="text" name="idLuogo" value="<?php echo $id; ?>" class="d-none"></input>
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
        }
      ?>

      <!-- BOTTONE + -->
      <div class="col-12 col-md-6 col-lg-4 py-3">
        <button type="button" class="btn btn-outline-success w-100 h-100" data-bs-toggle="modal" data-bs-target="#aggiungiLuogo">
          <h1 class="fs-1">+</h1>
        </button>
      </div>

     </div>
  </div> 

  <!-- Modal BOTTONE + -->
  <div class="modal fade" id="aggiungiLuogo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Aggiungi luogo</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="./aggiungiLuogo.php" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="denominazione">Denominazione</label> <br>
                        <input type="text" id="denominazione" class="w-100 form-control" maxlength="30" name="denominazione" placeholder="Nome del Luogo" required>
                    </div>
                    <div class="row mb-3">
                      <div class="col-9">
                        <label for="indirizzo">Indirizzo</label> <br>
                        <input type="text" name="indirizzo" class="w-100 form-control" maxlength="30" placeholder="Via di Prova" required>
                      </div>
                      <div class="col-3">
                        <label for="civico">Civico</label> <br>
                        <input type="text" name="civico" class="w-100 form-control" maxlength="5" placeholder="1" required>
                      </div>
                    </div>
                    <div class="mb-3">
                        <label for="citta">Citta</label> <br>
                        <input type="text" name="citta" class="w-100 form-control" maxlength="30" placeholder="Pistoia (PT)" required>
                    </div>
                    <div class="form-floating">
                      <textarea class="form-control"  name="note" class="w-100 form-control" id="noteBlock" style="height: 100px"></textarea>
                      <label for="noteBlock">Note</label>
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