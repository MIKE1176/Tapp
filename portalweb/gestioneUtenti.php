<?php
  include("session.php");
  check_auth();

  if($_SESSION['auth']!="AMMINISTRATIVO"){
    header("location: index.php");
    exit();
  }
  //query che mi ricerca tutti i utenti
  $queryUtenti = "SELECT * FROM utente WHERE utente.id!=1 order by utente.cognome asc";
  $utenti = mysqli_query($db, $queryUtenti);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
  <title>TAPP - Gestione Utenti</title>
</head>

<body>
  <?php include("./navbar.php");?>

  <script>
      [].slice.call(document.getElementsByClassName("nav-link")).forEach(element => {
          element.classList.remove("fw-bold");
      });
      if(document.getElementById("gestioneUtenti")) {
        document.getElementById("gestioneUtenti").classList.add("fw-bold");
      }
  </script>

  <?php
    if (isset($_SESSION['errore'])) {
      if ($_SESSION['errore'] == "cfUtente") {
        echo "<script>alert('Codice Fiscale del utente già inserito. Utente non creato.');</script>";
      } elseif ($_SESSION['errore'] == "mUtenti") {
        echo "<script>alert('Codice Fiscale del utente che stai modificando è gia inserito. Utente non modificato.');</script>";
      }
      unset($_SESSION['errore']);
    }
  ?>

  <!-- VISUALIZZA UTENTI DISATTIVATI -->
  <?php
    $visualizzaDisattivati = 0;
    $valoreRisultato = "";
    if (isset($_POST["utentiDisattivatiN"])) {
      $visualizzaDisattivati = $_POST['utentiDisattivatiN'];
      if ($visualizzaDisattivati == 1) {
        $valoreRisultato = "checked";
      } else {
        $valoreRisultato = "";
      }
    }
  ?>

  <div class="container mt-4">
      <div class="row justify-content-center mb-3">
        <div class="col-12 text-center">
            <form class="form-check form-switch text-center" id="formToggle" action="" method="POST">
                <div class="form-check form-check-inline">
                    <input class="d-none" type="number" value="<?php echo $visualizzaDisattivati;?>" name="utentiDisattivatiN" id="utentiN">
                    <input class="form-check-input" type="checkbox" name="utentiDisattivati" id="flexCheckDefault" onchange="updateValue()" <?php  echo $valoreRisultato;?>>
                    <label class="form-check-label" for="flexCheckDefault">Visualizza utenti disattivati</label>
                </div>
            </form>
        </div>
      </div>
      <div class="row justify-content-center mb-4">
        <div class="col-12 col-md-6">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0">🔍</span>
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cerca per nome o cognome...">
            </div>
        </div>
      </div>
  </div>

  <script>
      function updateValue() {
          let inputN = document.getElementById("utentiN");
          inputN.value = (inputN.value == "0") ? "1" : "0";
          document.getElementById("formToggle").submit();
      }
  </script>
  
  <!-- TABELLA UTENTI ATTIVI/DISATTIVATI -->

  <div class="table-responsive px-3">
    <table class="table table-hover">
      <thead>
        <tr>
          <th scope="col">Cognome</th>
          <th scope="col">Nome</th>
          <th scope="col">Sesso</th>
          <th scope="col">Indirizzo</th>
          <th scope="col">Telefono</th>
          <th scope="col">Note</th>
          <th scope="col"></th>
          <th scope="col"></th>
          <th scope="col"></th>
        </tr>
      </thead>
      <tbody>
        <?php
        //cerco gli utenti
        if (mysqli_num_rows($utenti) > 0) {
          while ($row = mysqli_fetch_assoc($utenti)) {        //CON mysql_fetch_assoc($utenti) ottengo il risultato riga per riga dei utenti

            //-------------------------  DATI UTENTI  -------------------------

            $id = $row['ID'];
            $cf = $row['CF'];
            $nome = $row['nome'];
            $cognome = $row['cognome'];
            $dataNascita = $row['dataNascita'];
            $luogoNascita = $row['luogoNascita'];
            $sesso = $row['sesso'];
            $indirizzo = $row['indirizzo'];
            $civico = $row['civico'];
            $citta = $row['citta'];
            $telefono = $row['telefono'];
            $noteUtente = $row['noteUtente'];
            $attivo = $row['attivo'];


            if ($visualizzaDisattivati == 0 && !$attivo) continue;

            $row_class = (!$attivo) ? 'table-danger' : '';
            $id = $row['ID'];
        ?>
        <tr class="<?php echo $row_class; ?>">
          <td><?php echo $row['cognome']; ?></td>
          <td><?php echo $row['nome']; ?></td>
          <td><?php echo $row['sesso']; ?></td>
          <td><?php echo $row['indirizzo'] . ', ' . $row['civico'] . " - " . $row['citta']; ?></td>
          <td><?php echo $row['telefono']; ?></td>
          <td><small><?php echo $row['noteUtente']; ?></small></td>
          <td>
            <button class="btn btn-sm btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#datiUtenteModal<?php echo $id; ?>">Dati</button>
          </td>
          <td>
            <button class="btn btn-sm btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#modificaUtenteModal<?php echo $id; ?>">Modifica</button>
          </td>
          <td>
            <button class="btn btn-sm btn-<?php echo (!$attivo) ? 'success' : 'outline-danger'; ?> w-100" data-bs-toggle="modal" data-bs-target="#modalToggle<?php echo $id; ?>">
              <?php echo (!$attivo) ? "Attiva" : "Disattiva"; ?>
            </button>
          </td>
        </tr>
        <?php 
          } // fine while
          mysqli_data_seek($utenti, 0);
        } else {
          echo "<tr><td colspan='9' class='text-center'>Nessun utente trovato.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <?php
    if (mysqli_num_rows($utenti) != 0) {
      while ($row = mysqli_fetch_assoc($utenti)) {        //CON mysql_fetch_assoc($utenti) ottengo il risultato riga per riga dei utenti
        //-------------------------  DATI UTENTI  -------------------------

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
        $telefono = $row['telefono'];
        $noteUtente = $row['noteUtente'];
        $attivo = $row['attivo'];
    ?>
      <!-- modal DATI UTENTE -->
      <div class="modal fade" id="<?php echo 'datiUtenteModal' . $id; ?>" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5 fw-bold" id="exampleModalLabel">DATI UTENTE</h1>
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
              <div class="mb-3">
                <label for="nascita">Luogo - Data Nascita </label> <br>
                <input type="text" name="nascita" value="<?php echo $luogoNascita . " - " . date_format(date_create_from_format('Y-m-d', $dataNascita), 'd/m/Y'); ?>" class="w-100 form-control fw-bold text-center" readonly>
              </div>
              <div class="mb-3">
                <label for="indirizzo">Indirizzo di Residenza</label> <br>
                <input type="text" name="indirizzo" value="<?php echo $indirizzo . ', ' . $civico . " - " . $citta; ?>" class="w-100 form-control fw-bold" readonly>
              </div>
              <div class="mb-3">
                <label for="codiceMezzo">Note utente</label> <br>
                <textarea name="noteUtente" class="w-100 form-control" readonly><?php echo $noteUtente; ?></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- modal MODIFICA UTENTE -->
      <div class="modal fade" id="<?php echo 'modificaUtenteModal' . $id; ?>" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">MODIFICA "
                <?php echo $nome . " " . $cognome; ?>"
              </h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="./modificaUtente.php" method="post">
              <input type="text" name="idUtente" value="<?php echo $id; ?>" class="d-none"></input>
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
                  <input type="text" name="cf" value="<?php echo $cf; ?>" class="w-100 form-control" maxlength="16" pattern="[A-Za-z]{6}\d{2}[A-Za-z]\d{2}[A-Za-z]\d{3}[A-Za-z]" placeholder="RSSMRA80L05F593A">
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
                <div class="mb-3">
                  <label for="telefono">Numero di telefono</label> <br>
                  <input type="tel" name="telefono" value="<?php echo $telefono; ?>" maxlength="10" class="w-100 form-control" required>
                </div>
                <div class="mb-3">
                  <label for="noteUtente">Note utente</label> <br>
                  <textarea name="noteUtente" class="w-100 form-control"><?php echo $noteUtente; ?></textarea>
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

      <!-- modal DISATTIVA UTENTE -->
      <div class="modal fade" id="<?php echo 'modalToggle' . $id; ?>" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">
                <?php
                if (!$attivo) {
                  echo "Attivazione utente";
                } else {
                  echo "Disattivazione utente";
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
              l'utente
              <?php echo $nome . ' ' . $cognome . ' (#' . $id . ')'; ?>?
            </div>
            <div class="modal-footer justify-content-center">
              <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Annulla</button>
              <form action="./toggleUtente.php" method="post">
                <input type="text" name="idUtente" value="<?php echo $id; ?>" class="d-none"></input>
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
    <!-- bottone AGGIUNGI UTENTE -->
    <button class="btn btn-outline-success w-100 h-100" data-bs-toggle="modal" data-bs-target="#aggiungiUtenteModal">
      <span class="fs-1 fw-bold text-center">+</span>
    </button>
    <!-- modal AGGIUNGI UTENTE -->
    <div class="modal fade" id="aggiungiUtenteModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Aggiungi utente</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="./aggiungiUtente.php" method="post">
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
                <input type="text" name="cf" class="w-100 form-control" maxlength="16" pattern="[A-Za-z]{6}\d{2}[A-Za-z]\d{2}[A-Za-z]\d{3}[A-Za-z]" placeholder="RSSMRA80L05F593A">
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
              <div class="mb-3">
                <label for="telefono">Numero di telefono</label> <br>
                <input type="tel" name="telefono" maxlength="10" class="w-100 form-control" required>
              </div>
              <div class="mb-3">
                <label for="noteUtente">Note utente</label> <br>
                <textarea name="noteUtente" class="w-100 form-control"> </textarea>
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
  <script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        // Recupera il valore inserito e trasformalo in minuscolo per un confronto case-insensitive
        let searchTerm = this.value.toLowerCase();
        
        // Seleziona tutte le righe della tabella (tr) all'interno del corpo (tbody)
        let rows = document.querySelectorAll('table tbody tr');

        rows.forEach(row => {
            // Estraiamo il testo delle prime due colonne (Cognome e Nome)
            let cognome = row.cells[0] ? row.cells[0].textContent.toLowerCase() : "";
            let nome = row.cells[1] ? row.cells[1].textContent.toLowerCase() : "";
            
            // Se il termine cercato è incluso nel nome o nel cognome, mostra la riga, altrimenti nascondila
            if (cognome.includes(searchTerm) || nome.includes(searchTerm)) {
                row.style.display = ""; // Mostra la riga
            } else {
                // Se la riga contiene il messaggio "Nessun utente trovato", non nasconderla se la tabella è vuota
                if (!row.classList.contains('text-center')) {
                    row.style.display = "none"; // Nasconde la riga
                }
            }
        });
    });
  </script>
</body>

</html>