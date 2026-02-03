<?php
    include("session.php");
    $idUtente = $_SESSION['id'];
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#BB0000">
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
    <title>Le tue prenotazioni</title>
</head>

<body class="ubuntu-regular" style="background-color: rgb(187, 0, 0);">

    <div class="d-flex align-items-center m-4 bg-danger-subtle rounded-5 p-1">
        <button class="btn btn-danger rounded-5 w-100" onclick="window.location.href='home.php'">
            <div class="hstack d-flex align-items-center">
                <i class="bi bi-arrow-bar-left h1 m-0 p-0"></i>
                <div class="vr ms-3"></div>
                <div class="d-flex w-100 justify-content-center">
                    <p class="m-0 p-0 h1">Indietro</p>
                </div>
            </div>
        </button>
    </div>

    <div class="m-4 bg-light rounded-5 p-4">
        <h2>Prenotazioni attive</h2>
        <hr class="m-0">
        <div class="vstack gap-3 d-flex justify-content-center py-4">
        <?php
        $query = mysqli_query($db, "select prenotazione.id as idPrenotazione, obiettivo.nome as nomeObiettivo, destinazione.nome as nomeDestinazione, data, TIME_FORMAT(`durata`, '%H:%i') as durata, scopo.nome as nomeScopo from prenotazione join scopo on idScopo = scopo.id join puntoInteresse as obiettivo on idObiettivo = obiettivo.id join puntoInteresse as destinazione on idDestinazione = destinazione.id where data >= NOW() and idPaziente = '$idUtente'");
        if (mysqli_num_rows($query) != 0) {
            while ($row = mysqli_fetch_assoc($query)) {
                $idPrenotazione = $row['idPrenotazione'];
                $nomeObiettivo = (isset($row['nomeObiettivo'])) ? $row['nomeObiettivo'] : "La tua abitazione";
                $nomeDestinazione = $row['nomeDestinazione'];
                $data = DateTime::createFromFormat('Y-m-d H:i:s', $row['data']) -> format('d/m/Y H:i');
                $durata = $row['durata'];
                $scopo = (isset($row['nomeScopo'])) ? $row['nomeScopo'] : 1;
                $HTML = <<<HTML
                <div class="card p-2 w-100 border-0 rounded-5 bg-danger-subtle" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title">$scopo - $data</h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary">$durata ore</h6>
                        <p class="card-text">Trasporto da $nomeObiettivo a $nomeDestinazione</p>
                        <form action="eliminaPrenotazione.php" method="post">
                            <input type="number" class="d-none" name="idPrenotazione" value="$idPrenotazione">
                            <button type="submit" class="btn btn-danger w-100">Annulla</button>
                        </form>
                    </div>
                </div>
                HTML;
                echo $HTML;
            }
        }else{
            echo <<<HTML
                <div class="d-flex justify-content-center p-5">
                    <p>Non hai nessuna prenotazione attiva</p>
                </div>
            HTML;
        }
        ?>
        </div>
        <h2>Prenotazioni precedenti</h2>
        <hr class="m-0">
        <div class="vstack gap-3 d-flex justify-content-center py-4">
        <?php
        $query = mysqli_query($db, "select prenotazione.id as idPrenotazione, obiettivo.nome as nomeObiettivo, destinazione.nome as nomeDestinazione, data, durata, scopo.nome as nomeScopo from prenotazione join scopo on idScopo = scopo.id join puntoInteresse as obiettivo on idObiettivo = obiettivo.id join puntoInteresse as destinazione on idDestinazione = destinazione.id where data < NOW() and idPaziente = '$idUtente'");
        if (mysqli_num_rows($query) != 0) {
            while ($row = mysqli_fetch_assoc($query)) {
                $nomeObiettivo = $row['nomeObiettivo'];
                $nomeDestinazione = $row['nomeDestinazione'];
                $data = DateTime::createFromFormat('Y-m-d H:i:s', $row['data']) -> format('d/m/Y H:i');
                $durata = $row['durata'];
                $scopo = $row['nomeScopo'];
                $HTML = <<<HTML
                <div class="card p-2 w-100 border-0 rounded-5 bg-danger-subtle" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title">$scopo - $data</h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary">$durata ore</h6>
                        <p class="card-text">Trasporto da $nomeObiettivo a $nomeDestinazione</p>
                    </div>
                </div>
                HTML;
                echo $HTML;
            }
        }else{
            echo <<<HTML
                <div class="d-flex justify-content-center p-5">
                    <p>Non hai nessuna prenotazione precedente</p>
                </div>
            HTML;
        }
        ?>
        </div>
    </div>
</body>

</html>