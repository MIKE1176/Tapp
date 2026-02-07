<?php
include('./session.php');

//query che mi ricerca tutti i pazienti
$queryPazienti = "SELECT * FROM paziente WHERE paziente.id!=1 and paziente.id!=2 and paziente.id!=3 and paziente.id!=4 order by paziente.cognome asc";
$pazienti = mysqli_query($db, $queryPazienti);

if($_SESSION['responsabile']!="AMMINISTRATIVO" && $_SESSION['responsabile']!="CONTABILITA"){
  header("location: index.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <link rel="icon" type="image/x-icon" href="./assets/favicon.ico">
  <title>Misegello - Gestione Pazienti</title>
</head>

<body>
  <?php
  include("./navbar.php");
  ?>
  <script>
      [].slice.call(document.getElementsByClassName("nav-link")).forEach(element => {
          element.classList.remove("fw-bold");
      });
      document.getElementById("gestionePazienti").classList.add("fw-bold");
  </script>
  <?php
    if (isset($_SESSION['errore'])) {
      if ($_SESSION['errore'] == "username") {
        echo "<script>alert('Codice Fiscale del paziente già inserito. Paziente non creato.');</script>";
        unset($_SESSION['errore']);
      } elseif ($_SESSION['errore'] == "mPazienti") {
        echo "<script>alert('Codice Fiscale del paziente che stai modificando è gia inserito. Paziente non modificato.');</script>";
        unset($_SESSION['errore']);
      }
    }
  ?>

  <!-- VISUALIZZA PAZIENTI DISATTIVATI -->
  <?php
  $visualizzaDisattivati = 0;
  $valoreRisultato = "";
  if (isset($_POST["pazientiDisattivatiN"])) {
    $visualizzaDisattivati = $_POST['pazientiDisattivatiN'];
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
                      <input class="d-none" type="number" value="<?php echo $visualizzaDisattivati;?>" name="pazientiDisattivatiN" id="pazientiN">
                      <input class="form-check-input" type="checkbox" name="pazientiDisattivati" id="flexCheckDefault" onchange="updateValue()" <?php  echo $valoreRisultato;?>>
                      <label class="form-check-label" for="flexCheckDefault">Visualizza pazienti disattivati</label>
                  </div>
              </form>
          </div>
      </div>
  </div>

  <script>
      function updateValue() {
          if(document.getElementById("pazientiN").value == "0"){
              document.getElementById("pazientiN").value = "1";
          }else{
              document.getElementById("pazientiN").value = "0";
          }
          document.getElementById("formToggle").submit();
      }
  </script>
  
  <!-- TABELLA PAZIENTI ATTIVI/DISATTIVATI -->

  <div class="table-responsive px-3">
    <table class="table table-hover">
      <thead>
        <tr>
          <th scope="col">Cognome</th>
          <th scope="col">Nome</th>
          <th scope="col">Sesso</th>
          <th scope="col">Indirizzo</th>
          <th scope="col">Socio?</th>
          <th scope="col">Telefono</th>
          <th scope="col">Note</th>
          <th scope="col"></th>
          <th scope="col"></th>
          <th scope="col"></th>
        </tr>
      </thead>
      <tbody>
        <?php
        //cerco i pazienti
        if (mysqli_num_rows($pazienti) != 0) {
          while ($row = mysqli_fetch_assoc($pazienti)) {        //CON mysql_fetch_assoc($pazienti) ottengo il risultato riga per riga dei pazienti

            //-------------------------  DATI PAZIENTI  -------------------------

            $id = $row['ID'];
            $nome = $row['nome'];
            $cognome = $row['cognome'];
            $cf = $row['CF'];
            $dataNascita = $row['dataNascita'];
            $luogoNascita = $row['luogoNascita'];
            $sesso = $row['sesso'];
            $indirizzo = $row['indirizzo'];
            $civico = $row['civico'];
            $citta = $row['citta'];
            $socio = $row['socio'];
            $telefono = $row['telefono'];
            $notePaziente = $row['notePaziente'];
            $attivo = $row['attivo'];


            if (!($visualizzaDisattivati == 0 and !$attivo)) {
              $row_class = "";
              if ($row['socio']) {
                $row_class = 'table-warning';
              }
              if ($visualizzaDisattivati == 1 and !$attivo) {
                $row_class = 'table-danger';
              }
        ?>
              <tr>
                <th scope="row" class="<?php echo $row_class; ?>">
                  <?php echo $cognome; ?>
                </th>
                <th scope="row" class="<?php echo $row_class; ?>">
                  <?php echo $nome; ?>
                  </td>
                <td class="<?php echo $row_class; ?>">
                  <?php echo $sesso; ?>
                </td>
                <td class="<?php echo $row_class; ?>">
                  <?php echo $indirizzo . ', ' . $civico . " - " . $citta; ?>
                </td>
                <td class="<?php echo $row_class; ?>">
                  <?php
                  if ($socio == 1) {
                    echo 'Sì';
                  } else {
                    echo 'No';
                  }
                  ?>
                </td>
                <td class="<?php echo $row_class; ?>">
                  <?php echo $telefono; ?>
                </td>
                <td class="<?php echo $row_class; ?>">
                  <?php echo $notePaziente; ?>
                </td>
                <td class="<?php echo $row_class; ?>">
                  <button class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#<?php echo 'datiPazienteModal' . $id; ?>">
                    Dati
                  </button>
                </td>
                <td class="<?php echo $row_class; ?> text-center">
                  <button class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#<?php echo 'modificaPazienteModal' . $id; ?>">
                    Modifica
                  </button>
                </td>
                <td class="<?php echo $row_class; ?> text-center">
                  <button class="btn btn-<?php
                                          if (!$attivo) {
                                            echo "success";
                                          } else {
                                            echo "outline-danger";
                                          }
                                          ?>" data-bs-toggle="modal" data-bs-target="#<?php echo 'modalToggle' . $id; ?>">
                    <?php
                    if (!$attivo) {
                      echo "Attiva";
                    } else {
                      echo "Disattiva";
                    }
                    ?>
                  </button>
                </td>
              </tr>
        <?php
            }
          }
        } else {
          echo "Nessun paziente trovato.";
        }
        ?>
      </tbody>
    </table>
  </div>

  <?php
  $pazienti = mysqli_query($db, $queryPazienti);

  if (mysqli_num_rows($pazienti) != 0) {
    while ($row = mysqli_fetch_assoc($pazienti)) {        //CON mysql_fetch_assoc($pazienti) ottengo il risultato riga per riga dei pazienti
      //-------------------------  DATI PAZIENTI  -------------------------

      $id = $row['ID'];
      $nome = $row['nome'];
      $cognome = $row['cognome'];
      $cf = $row['CF'];
      $dataNascita = $row['dataNascita'];
      $luogoNascita = $row['luogoNascita'];
      $sesso = $row['sesso'];
      $indirizzo = $row['indirizzo'];
      $civico = $row['civico'];
      $citta = $row['citta'];
      $socio = $row['socio'];
      $telefono = $row['telefono'];
      $notePaziente = $row['notePaziente'];
      $attivo = $row['attivo'];
  ?>
      <!-- modal DATI PAZIENTE -->
      <div class="modal fade" id="<?php echo 'datiPazienteModal' . $id; ?>" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5 fw-bold" id="exampleModalLabel">DATI PAZIENTE</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <div class="input-group">
                  <span class="input-group-text">Nominativo</span>
                  <input type="text" name="nominativo" value="<?php echo strtoupper($nome) . " " . strtoupper($cognome) . " (" . $sesso . ")"; ?>" class="form-control fw-bold text-center" readonly>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-6">
                  <label for="targa">Codice Fiscale</label> <br>
                  <input type="text" name="cf" value="<?php echo $cf; ?>" class="w-100 form-control fw-bold text-center" readonly>
                </div>
                <div class="col-6">
                  <label for="telefono">Telefono</label> <br>
                  <input type="tel" name="telefono" value="<?php echo $telefono; ?>" class="w-100 form-control fw-bold text-center" readonly>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-9">
                  <label for="nascita">Luogo - Data Nascita </label> <br>
                  <input type="text" name="nascita" value="<?php echo $luogoNascita . " - " . date_format(date_create_from_format('Y-m-d', $dataNascita), 'd/m/Y'); ?>" class="w-100 form-control fw-bold text-center" readonly>
                </div>
                <div class="col-3">
                  <label for="socio">Socio?</label> <br>
                  <input type="text" name="socio" value="<?php
                                                          if ($socio == 1) {
                                                            echo "SI";
                                                          } elseif ($socio == 0) {
                                                            echo "NO";
                                                          }
                                                          ?>" class="w-100 form-control fw-bold text-center" readonly>
                </div>
              </div>
              <div class="mb-3">
                <label for="indirizzo">Indirizzo di Residenza</label> <br>
                <input type="text" name="indirizzo" value="<?php echo $indirizzo . ', ' . $civico . " - " . $citta; ?>" class="w-100 form-control fw-bold" readonly>
              </div>
              <div class="mb-3">
                <label for="codiceMezzo">Note paziente</label> <br>
                <textarea name="notePaziente" class="w-100 form-control" readonly><?php echo $notePaziente; ?></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- modal MODIFICA PAZIENTE -->
      <div class="modal fade" id="<?php echo 'modificaPazienteModal' . $id; ?>" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">MODIFICA "
                <?php echo $nome . " " . $cognome; ?>"
              </h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="./modificaPaziente.php" method="post">
              <input type="text" name="idPaziente" value="<?php echo $id; ?>" class="d-none"></input>
              <div class="modal-body">
                <div class="mb-3">
                  <div class="input-group">
                    <span class="input-group-text">Nome e cognome</span>
                    <input type="text" placeholder="Mario" maxlength="30" name="nome" value="<?php echo $nome; ?>" class="form-control" maxlength="30" required>
                    <input type="text" placeholder="Rossi" maxlength="30" name="cognome" value="<?php echo $cognome; ?>" class="form-control" maxlength="30" required>
                  </div>
                </div>
                <div class="mb-3">
                  <label for="targa">Codice Fiscale</label> <br>
                  <input type="text" name="cf" value="<?php echo $cf; ?>" class="w-100 form-control" maxlength="20" pattern="[A-Za-z]{6}\d{2}[A-Za-z]\d{2}[A-Za-z]\d{3}[A-Za-z]" placeholder="RSSMRA80L05F593A">
                </div>
                <div class="mb-3">
                  <label for="codiceMezzo">Data di nascita</label> <br>
                  <input type="date" name="dataNascita" value="<?php echo $dataNascita; ?>" class="w-100 form-control" required>
                </div>
                <div class="mb-3">
                  <label for="codiceMezzo">Luogo di nascita</label> <br>
                  <input type="text" name="luogoNascita" value="<?php echo $luogoNascita; ?>" maxlength="30" class="w-100 form-control" required>
                </div>
                <div class="mb-3">
                  <label for="floatingInput">Sesso</label> <br>
                  <select class="form-select" name="sesso">
                    <option value="M" <?php if ($sesso == "M")
                                        echo " selected"; ?>>Maschio</option>
                    <option value="F" <?php if ($sesso == "F")
                                        echo " selected"; ?>>Femmina</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="indirizzo">Indirizzo</label> <br>
                  <input type="text" name="indirizzo" value="<?php echo $indirizzo; ?>" maxlength="30" class="w-100 form-control" required>
                </div>
                <div class="mb-3">
                  <label for="civico">Civico</label> <br>
                  <input type="text" name="civico" value="<?php echo $civico; ?>" maxlength="5" class="w-100 form-control" required>
                </div>
                <div class="mb-3">
                  <label for="citta">Città</label> <br>
                  <input type="text" name="citta" value="<?php echo $citta; ?>" maxlength="30" class="w-100 form-control" required>
                </div>
                <div class="row mb-3">
                  <div class="col-6">
                    <span>È socio?</span>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="socio" id="socioTrue" value="True" <?php if ($socio == 1)
                                                                                                              echo " checked"; ?>>
                      <label class="form-check-label" for="socioTrue">
                        Sì
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="socio" id="socioFalse" value="False" <?php if ($socio == 0)
                                                                                                                echo " checked"; ?>>
                      <label class="form-check-label" for="socioFalse">
                        No
                      </label>
                    </div>
                  </div>
                  <div class="col-6">
                    <label for="telefono">Numero di telefono</label> <br>
                    <input type="tel" name="telefono" value="<?php echo $telefono; ?>" class="w-100 form-control" required>
                  </div>
                </div>
                <div class="mb-3">
                  <label for="notePaziente">Note paziente</label> <br>
                  <textarea name="notePaziente" class="w-100 form-control"><?php echo $notePaziente; ?></textarea>
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

      <!-- modal DISATTIVA PAZIENTE -->
      <div class="modal fade" id="<?php echo 'modalToggle' . $id; ?>" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">
                <?php
                if (!$attivo) {
                  echo "Attivazione paziente";
                } else {
                  echo "Disattivazione paziente";
                }
                ?>
              </h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              Sei sicuro di voler
              <?php
              if (!$attivo) {
                echo "attivare";
              } else {
                echo "disattivare";
              }
              ?>
              il paziente
              <?php echo $nome . ' ' . $cognome . ' (#' . $id . ')'; ?>?
            </div>
            <div class="modal-footer justify-content-center">
              <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Annulla</button>
              <form action="./togglePaziente.php" method="post">
                <input type="text" name="disattivaPaziente" value="<?php echo $id; ?>" class="d-none"></input>
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
  <?php
    }
  }
  ?>
  <div class="container">
    <!-- bottone AGGIUNGI PAZIENTE -->
    <button class="btn btn-outline-success w-100 h-100" data-bs-toggle="modal" data-bs-target="#aggiungiPazienteModal">
      <span class="fs-1 fw-bold text-center">+</span>
    </button>
    <!-- modal AGGIUNGI PAZIENTE -->
    <div class="modal fade" id="aggiungiPazienteModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Aggiungi paziente</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="./aggiungiPaziente.php" method="post">
            <div class="modal-body">
              <div class="mb-3">
                <div class="input-group">
                  <span class="input-group-text">Nome e cognome</span>
                  <input type="text" placeholder="Mario" maxlength="30" name="nome" class="form-control" maxlength="30" required>
                  <input type="text" placeholder="Rossi" maxlength="30" name="cognome" class="form-control" maxlength="30" required>
                </div>
              </div>
              <div class="mb-3">
                <label for="targa">Codice Fiscale</label> <br>
                <input type="text" name="cf" class="w-100 form-control" maxlength="20" pattern="[A-Za-z]{6}\d{2}[A-Za-z]\d{2}[A-Za-z]\d{3}[A-Za-z]" placeholder="RSSMRA80L05F593A">
              </div>
              <div class="mb-3">
                <label for="dataNascita">Data di nascita</label> <br>
                <input type="date" name="dataNascita" class="w-100 form-control" required>
              </div>
              <div class="mb-3">
                <label for="luogoNascita">Luogo di nascita</label> <br>
                <input type="text" name="luogoNascita" maxlength="30" class="w-100 form-control" required>
              </div>
              <div class="mb-3">
                <label for="sesso">Sesso</label> <br>
                <select class="form-select" name="sesso">
                  <option value="M">Maschio</option>
                  <option value="F">Femmina</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="indirizzo">Indirizzo</label> <br>
                <input type="text" name="indirizzo" maxlength="30" class="w-100 form-control" required>
              </div>
              <div class="mb-3">
                <label for="civico">Civico</label> <br>
                <input type="text" name="civico" maxlength="5" class="w-100 form-control" required>
              </div>
              <div class="mb-3">
                <label for="citta">Città</label> <br>
                <input type="text" name="citta" maxlength="30" class="w-100 form-control" required>
              </div>
              <div class="row mb-3">
                <div class="col-6">
                  <span>È socio?</span>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="socio" id="socioTrue" value="True" checked>
                    <label class="form-check-label" for="socioTrue">
                      Sì
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="socio" id="socioFalse" value="False">
                    <label class="form-check-label" for="socioFalse">
                      No
                    </label>
                  </div>
                </div>
                <div class="col-6">
                  <label for="telefono">Numero di telefono</label> <br>
                  <input type="tel" name="telefono" class="w-100 form-control" required>
                </div>
              </div>
              <div class="mb-3">
                <label for="notePaziente">Note paziente</label> <br>
                <textarea name="notePaziente" class="w-100 form-control"> </textarea>
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
</body>

</html>