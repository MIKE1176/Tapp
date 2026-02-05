<?php
    include("./session.php");
    check_auth(true); // Se sono già loggato, mi manda a home.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#e5e5e5">
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
    <title>Benvenuto!</title>
</head>

<body class="ubuntu-regular vh-100 justify-content-center p-3 accessoBody">
    <div class="vstack gap-4 p-4 mx-auto" style="max-width: 500px;">
        <img src="../assets/logIn.svg" alt="Immagine login" class="img-fluid" style="filter: drop-shadow(0 0 1rem #ffffff)">
        <h1 class="w-100 text-center display-5 fw-medium mt-0 mb-0">Felice di vederti!</h1>
        <p class="text-muted h6 text-center mt-0 mb-0">Se hai già un account premi "Accedi", altrimenti clicca su "Registrati"
            per crearne uno.</p>
        <hr class="p-0 m-0">
        <button type="submit" class="btn btn-dark rounded-3 text-center py-3 w-100" onclick="window.location.href='accedi.php'"><p class="h3 m-0 p-0">Accedi</p></button> <!--  -->
        <button type="button" class="btn btn-secondary rounded-3 text-center mx-4 py-2" onclick="window.location.href='registrati-scroto-0.html'" disabled><p class="h3 m-0 p-0">Registrati</p></button>
    </div>
</body>

</html>