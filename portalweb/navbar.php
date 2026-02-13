<?php
$html = <<<HTML

<nav class="navbar navbar-expand-lg bg-info">
  <div class="container-fluid">
    <a class="navbar-brand" href="./index.php">
      <img src="../assets/icons/logo.jpg" alt="Bootstrap" width="30" height="30">
    </a>
    <a class="navbar-brand" href="./index.php">
      Tapp
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02"
      aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link text-center" href="operatoreMissioni.php" id="operatoreMissioni">I miei Servizi</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-center" href="trasportiDaAssegnare.php" id="trasportiDaAssegnare">Richieste Trasporti</a>
        </li>
HTML;

if($_SESSION['auth'] == "AMMINISTRATIVO"){
  $html .= <<<HTML
      <li class="nav-item">
        <a class="nav-link text-center" href="operatoreTurni.php" id="operatoreTurni">I miei Turni</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-center" href="gestioneMissioni.php" id="gestioneMissioni">Gestione missioni</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-center" href="gestioneTurni.php" id="gestioneTurni">Gestione turni</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-center" href="gestioneUtenti.php" id="gestioneUtenti">Gestione utenti</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-center" href="gestioneOperatori.php" id="gestioneOperatori">Gestione operatori</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-center" href="gestioneLuoghi.php" id="gestioneLuoghi">Gestione luoghi</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-center" href="gestioneMezzi.php" id="gestioneMezzi">Gestione mezzi</a>
      </li>
  HTML;
}

$html .= <<<HTML
        <li class="nav-item justify-content-center text-center">
          <form class="d-lg-none" action="./logout.php" method="GET" role="search">
            <button class="btn btn-danger" type="submit" id="a">
              <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-box-arrow-right me-2" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
              </svg>
              Logout
            </button>
          </form>
        </li>
      </ul>
      <form class="d-flex me-2 d-none d-lg-inline" action="./logout.php" method="GET" role="search">
          <button class="btn btn-danger" type="submit">
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
              <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
            </svg>
          </button>
      </form>
    </div>
  </div>
</nav>
HTML;

echo $html;

