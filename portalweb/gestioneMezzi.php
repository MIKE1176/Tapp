<?php
  include("session.php");
  check_auth(); // Se non loggato o non attivo, scappa e va al login

  if($_SESSION['auth']!="AMMINISTRATIVO"){
    header("location: index.php");
  }
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Tapp - Gestione Mezzi</title>
</head>

<body>
    <?php
    include('./navbar.php');
    ?>
    <script>
        [].slice.call(document.getElementsByClassName("nav-link")).forEach(element => {
            element.classList.remove("fw-bold");
        });
        document.getElementById("gestioneMezzi").classList.add("fw-bold");
    </script>
    <?php
        if (isset($_SESSION['errore'])) {
            if ($_SESSION['errore'] == "codiceMezzo") {
                echo "<script>alert('Il codice mezzo che hai provato a inserire è già presente nel database. Mezzo non creato.');</script>";
                unset($_SESSION['errore']);
            } elseif ($_SESSION['errore'] == "targa") {
                echo "<script>alert('La targa che hai provato a inserire è già presente nel database. Mezzo non creato.');</script>";
                unset($_SESSION['errore']);
            }
        }
    ?>

    <!-- VISUALIZZA MEZZI DISATTIVATI -->
    <?php
    $visualizzaDisattivati = 0;
    $valoreRisultato = "";
    if (isset($_POST["mezziDisattivatiN"])) {
        $visualizzaDisattivati = $_POST['mezziDisattivatiN'];
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
                        <input class="d-none" type="number" value="<?php echo $visualizzaDisattivati; ?>"
                            name="mezziDisattivatiN" id="mezziN">
                        <input class="form-check-input" type="checkbox" name="mezziDisattivati" id="flexCheckDefault"
                            onchange="updateValue()" <?php echo $valoreRisultato; ?>>
                        <label class="form-check-label" for="flexCheckDefault">Visualizza mezzi disattivati</label>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateValue() {
            if (document.getElementById("mezziN").value == "0") {
                document.getElementById("mezziN").value = "1";
            } else {
                document.getElementById("mezziN").value = "0";
            }
            document.getElementById("formToggle").submit();
        }
    </script>

    <!-- VISUALIZZAZIONE MEZZI -->

    <div class="container vh-100">
        <div class="row">
            <?php
            // Recupero dei dati dei mezzi
            $queryAutomezzi = "SELECT automezzo.img,automezzo.targa,automezzo.codiceMezzo,automezzo.attivo FROM automezzo order by automezzo.codiceMezzo asc";
            $automezzi = mysqli_query($db, $queryAutomezzi);



            // Controllo se il numero dei mezzi è diverso da zero
            if (mysqli_num_rows($automezzi) != 0) {

                // Stampo i dati degli automezzi
                while ($row = mysqli_fetch_assoc($automezzi)) {        //CON mysql_fetch_assoc($automezzi) ottengo il risultato riga per riga, fino a che non finisce la stampa
            
                    //ottengo i vari dati riga per riga in queste variabili, così da inserirli nei vari campi della card-automezzo
                    $img = $row['img'];
                    $targa = $row['targa'];
                    $codiceMezzo = $row['codiceMezzo'];
                    $attivo = $row['attivo'];

                    if (!($visualizzaDisattivati == 0 and !$attivo)) {
                        $row_class = "";
                        if ($visualizzaDisattivati == 1 and !$attivo) {
                            $row_class = 'border border-3 border-danger';
                        }



                        ?>
                        <div class="col-12 col-md-6 col-lg-4 py-3">
                            <div class="card p-3 shadow <?php echo $row_class ?>">
                                <div class="row">
                                    <div class="col-6 d-flex align-items-center">
                                        <img src="<?php echo $img; ?>" alt="Immagine Automezzo" class="w-100 align-middle">
                                        <!-- Inserisco l'immagine corrispondente all'automezzo -->
                                    </div>
                                    <div class="col-6 d-flex align-items-center">
                                        <div>
                                            <div class="row">
                                                <h3 class="fw-bold">
                                                    <?php echo $codiceMezzo; ?>
                                                    </h4>
                                            </div>
                                            <div class="row">
                                                <p class="my-0">Targa</p>
                                                <h4>
                                                    <?php echo $targa; ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-<?php
                                if (!$attivo) {
                                    echo "success";
                                } else {
                                    echo "outline-danger";
                                }
                                ?>" data-bs-toggle="modal" data-bs-target="#<?php echo $targa; ?>">
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



                        <!-- Modal -->
                        <div class="modal fade" id="<?php echo $targa; ?>" tabindex="-1" aria-labelledby="exampleModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">
                                            <?php
                                                if (!$attivo) {
                                                    echo "Attivazione automezzo";
                                                } else {
                                                    echo "Disattivazione automezzo";
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
                                        il mezzo
                                        <?php echo $codiceMezzo; ?>?
                                    </div>
                                    <div class="modal-footer">
                                        <div class="d-grid gap-2 mx-auto">
                                            <form action="./toggleMezzo.php" method="post">
                                                <button type="button" class="btn btn-outline-success"
                                                    data-bs-dismiss="modal">Annulla</button>
                                                <input type="text" name="targa" value="<?php echo $targa; ?>"
                                                    class="d-none"></input>
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
            } else {
                echo "Nessun automezzo trovato.";
            }

            // Chiusura connessione al database
            mysqli_close($db);
            ?>
            <div class="col-12 col-md-6 col-lg-4 py-3">
                <button type="button" class="btn btn-outline-success w-100 h-100" data-bs-toggle="modal" data-bs-target="#aggiungiVeicoloModal">
                    <h1 class="fs-1">+</h1>
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="aggiungiVeicoloModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Aggiungi automezzo</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="./aggiungiMezzo.php" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="targa">Targa</label> <br>
                            <input type="text" id="targa" class="w-100 form-control" name="targa" maxlength="7"
                                pattern="[A-Z]{2}\d{3}[A-Z]{2}" placeholder="AA000AA" required>
                        </div>
                        <div class="mb-3">
                            <label for="codiceMezzo">Codice Mezzo</label> <br>
                            <input type="text" name="codiceMezzo" class="w-100 form-control" placeholder="Nome della Macchina"
                                maxlength="20" required>
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