<?php
    include("./session.php");
    check_auth(); // Se NON sono loggato, mi manda a accedi.php
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#FFFFFF">
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
    <title>Tapp - Servizi Web</title>
</head>

<body class="ubuntu-regular">
    <div class="vstack gap-3 vh-100 p-3">
    <button class="btn btn-primary p-3 p-sm-5 rounded-5 shadow flex-grow-1"
        onclick="window.location.href='formPrenotazione.php'">
        <h1 class="display-1 fw-semibold">RICHIEDI TRASPORTO</h1>
    </button>

    <button class="btn btn-danger p-3 p-sm-5 rounded-5 shadow flex-grow-1"
        onclick="window.location.href='prenotazioni.php'">
        <h1 class="display-1 fw-semibold">SERVIZI PRENOTATI</h1>
    </button>

    <button class="btn btn-success p-3 p-sm-5 rounded-5 shadow" style="background-color: rgb(24, 116, 24);"
        onclick="window.location.href='profilo.php'">
        <h1 class="display-1 fw-semibold">PROFILO</h1>
    </button>
</div>
</body>

</html>