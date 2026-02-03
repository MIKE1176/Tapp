<?php
    include ("session.php");
    $idUtente = $_SESSION['id'];
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#187418">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="manifest.json" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">
    <script src="./js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap');
    </style>

    <link rel="icon" href="../assets/favicon.ico" type="image/x-icon">
    <title>Profilo</title>
</head>

<body class="ubuntu-regular" style="background-color: rgb(24, 116, 24);">

    <div class="d-flex align-items-center m-4 bg-success-subtle rounded-5 p-1">
        <button class="btn btn-success rounded-5 w-100" onclick="window.location.href='home.php'">
            <div class="hstack d-flex align-items-center">
                <i class="bi bi-arrow-bar-left h1 m-0 p-0"></i>
                <div class="vr ms-3"></div>
                <div class="d-flex w-100 justify-content-center">
                    <p class="m-0 p-0 h1">Indietro</p>
                </div>
            </div>
        </button>
    </div>

    <?php
    $query = mysqli_query($db, "select utente.nome as nomeUtente, cognome, dataNascita, via, civico, capCitta, citta.nome as nomeCitta from utente join citta on capCitta = cap where utente.id = '$idUtente';");
    if (mysqli_num_rows($query) != 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            $nominativo = $row['nomeUtente'] . ' ' . $row['cognome'];
            $data = DateTime::createFromFormat('Y-m-d', $row['dataNascita'])->format('d/m/Y');
            $residenza = $row['via'] . ' ' . $row['civico'] . ', ' . $row['nomeCitta'] . ' ' . $row['capCitta'];
            $HTML = <<<HTML
            <div class="vstack gap-3 m-4">
                <div class="p-4 d-block align-items-center bg-light-subtle rounded-5">
                    <h3 class="m-0 p-0 fw-bold">NOMINATIVO</h3>
                    <hr class="mt-0">
                    <h4 class="m-0 p-0 ubuntu-regular">$nominativo</h4>
                </div>
                <div class="p-4 d-block align-items-center bg-light-subtle rounded-5">
                    <h3 class="m-0 p-0 fw-bold">DATA DI NASCITA</h3>
                    <hr class="mt-0">
                    <h4 class="m-0 p-0 ubuntu-regular">$data</h4>
                </div>
                <div class="p-4 d-block align-items-center bg-light-subtle rounded-5">
                    <h3 class="m-0 p-0 fw-bold">RESIDENZA</h3>
                    <hr class="mt-0">
                    <h4 class="m-0 p-0 ubuntu-regular">$residenza</h4>
                </div>
                <div class="p-4 d-block align-items-center bg-light-subtle rounded-5">
                    <h3 class="m-0 p-0 fw-bold">TESSERE ASSOCIAZIONI</h3>
                    <hr class="mt-0">
            HTML;
            $associazioni = mysqli_query($db, "select numero, associazione.nome as nomeAssociazione from tessera join associazione on idAssociazione = associazione.id where idUtente = '$idUtente'");
            if (mysqli_num_rows($associazioni) != 0) {
                $HTML .= <<<HTML
                    <ul style="list-style: square outside url('./assets/icons/caret-right-fill.svg')" class='ps-4'>
                HTML;
                while ($row = mysqli_fetch_assoc($associazioni)) {
                    $associazione = $row['nomeAssociazione'];
                    $numeroTessera = $row['numero'];
                    $HTML .= <<<HTML
                        <li class="ubuntu-regular">
                            <h4 class="m-0 mt-3 p-0">$associazione</h4>
                            <h5 class="text-muted">(Tessera #$numeroTessera)</h5>
                        </li>
                    HTML;
                }
                $HTML .= <<<HTML
                    </ul>
                HTML;
            } else {
                $HTML .= <<<HTML
                    <h4 class="m-0 p-0 ubuntu-regular">Non sei affiliato con nessuna associazione</h4>
                HTML;
            }

            $HTML .= <<<HTML
                </div>
                <div class="row">
                    <div class="col">
                        <form action="logout.php" method="post">
                            <button class="btn btn-danger fs-2 rounded-4 mt-3 w-100">Esci dal tuo profilo</button>
                        </form>
                    </div>
                    <div class="col d-none"> <!-- TODO -->
                        <form action="cambiaPassword.php" method="post">
                            <button class="btn btn-primary w-100">Cambia la password</button>
                        </form>
                    </div>
                </div>
            </div>
            HTML;
        }
    }
    echo $HTML;
    ?>
</body>

</html>