<?php
    include("./session.php");
    check_auth(true); // Se sono già loggato, mi manda a home.php
?>


<!DOCTYPE html>
<html lang="it">

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
    <title>Accesso - Tapp Servizi Web</title>
</head>

<body class="ubuntu-regular vh-100 d-block justify-content-center accessoBody" style="overflow-y: hidden; ">

    <div class="p-3 p-lg-4 mb-3 mx-auto h-100 bg-transparent" style="max-width: 500px;">
        <h1 class="w-100 text-center display-3">Accedi</h1>
        <hr class="mx-3">
        <?php
        if(isset($_SESSION['errore']) && $_SESSION['errore'] == 'credenzialiSbagliate'){
            echo <<<HTML
                <p class="text-danger text-center">Credenziali errate, riprova!</p>
            HTML;
        }
        ?>
        <form action="login.php" method="post">
            <div class="form-floating m-3 mb-3">
                <input type="text" class="form-control rounded-3" name="username" placeholder="antonio" pattern="^[A-Za-z' ]{1,30}$" required>
                <label for="floatingInput">Username</label>
            </div>
            <div class="form-floating m-3">
                <input type="password" class="form-control rounded-3" name="password" placeholder="antonio" pattern="^[A-Za-z0-9 ?!@]{1,30}$" required>
                <label for="floatingPassword">Password</label>
            </div>
            <div class="text-center w-100">
                <button type="submit" class="btn btn-primary rounded-pill p-2 px-3">
                    <h3 class="m-0 p-0">Accedi</h3>
                </button>
            </div>
        </form>

        <!-------------------------------------------------------------------------------------------->

        <div class="hstack gap-1 mx-3">
            <div class="w-100">
                <hr class="m-0">
            </div>
            <h1 class="w-100 text-center display-5">Oppure</h1>
            <div class="w-100">
                <hr class="m-0">
            </div>
        </div>

        <div class="vstack gap-3 m-3">
            <button type="button" disabled class="btn btn-secondary rounded-pill shadow-lg d-inline-flex align-items-center">
                <i class="bi bi-google me-auto"></i>
                <p class="m-0 p-0">Accedi con Google</p><i class="bi bi-caret-right-fill ms-auto"></i>
            </button>
            <button type="button" disabled class="btn btn-primary rounded-pill shadow-lg d-inline-flex align-items-center">
                <i class="bi bi-facebook me-auto"></i>
                <p class="m-0 p-0">Accedi con Facebook</p><i class="bi bi-caret-right-fill ms-auto"></i>
            </button>
            <button type="button" disabled class="btn btn-dark rounded-pill shadow-lg d-inline-flex align-items-center">
                <i class="bi bi-twitter-x me-auto"></i>
                <p class="m-0 p-0">Accedi con Twitter-X</p><i class="bi bi-caret-right-fill ms-auto"></i>
            </button>
            <button type="button" disabled class="btn btn-info rounded-pill shadow-lg d-inline-flex align-items-center">
                <i class="bi bi-person-vcard-fill me-auto"></i>
                <p class="m-0 p-0">Accedi con CIE</p><i class="bi bi-caret-right-fill ms-auto"></i>
            </button>
            <button type="button" disabled class="btn btn-warning rounded-pill shadow-lg d-inline-flex align-items-center">
                <i class="bi bi-person-circle me-auto"></i>
                <p class="m-0 p-0">Accedi con Spid</p><i class="bi bi-caret-right-fill ms-auto"></i>
            </button>
        </div>

        <!-------------------------------------------------------------------------------------------->
    </div>

</body>

</html>