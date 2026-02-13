<?php
include("session.php");
check_auth();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>TAPP - I Miei Servizi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <style>
        body { background-color: #f4f7f6; }
        .calendar-nav {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 30px;
        }
        .day-group {
            margin-bottom: 25px;
        }
        .day-header {
            font-size: 1.1rem;
            font-weight: 700;
            color: #495057;
            text-transform: uppercase;
            padding: 10px 0;
            border-bottom: 2px solid #0d6efd;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
        }
        .mission-card {
            border: none;
            border-left: 5px solid #198754;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.2s;
            margin-bottom: 10px;
        }
        .mission-card.ritorno { border-left-color: #ffc107; }
        
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: 15px;
            color: #6c757d;
        }
        .time-badge {
            font-size: 0.9rem;
            font-weight: bold;
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 5px;
        }
    </style>
</head>

<body onload="initPagina()">

  <?php
    include('./navbar.php');
  ?>
  <script>
      document.addEventListener("DOMContentLoaded", () => {
          const navLinks = document.querySelectorAll(".nav-link");
          navLinks.forEach(link => link.classList.remove("fw-bold"));
          const currentNav = document.getElementById("operatoreMissioni");
          if(currentNav) currentNav.classList.add("fw-bold");
      });
  </script>
<div class="container py-4">
    <h2 class="text-center fw-bold mb-4 text-primary">I MIEI SERVIZI</h2>

    <div class="calendar-nav">
        <div class="row g-2 align-items-center">
            <div class="col-2 col-md-1">
                <button class="btn btn-primary w-100" onclick="spostaSettimana(-7)"><i class="bi bi-chevron-left"></i></button>
            </div>
            <div class="col-8 col-md-10">
                <input type="date" id="dataPicker" class="form-control form-control-lg text-center fw-bold border-primary" onchange="caricaRecap()">
            </div>
            <div class="col-2 col-md-1">
                <button class="btn btn-primary w-100" onclick="spostaSettimana(7)"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </div>

    <div id="recapContainer">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Caricamento missioni...</p>
        </div>
    </div>
</div>

<script>
let dataCorrente = new Date();

function initPagina() {
    document.getElementById("dataPicker").value = dataCorrente.toISOString().split('T')[0];
    caricaRecap();
}

function spostaSettimana(giorni) {
    dataCorrente.setDate(dataCorrente.getDate() + giorni);
    document.getElementById("dataPicker").value = dataCorrente.toISOString().split('T')[0];
    caricaRecap();
}

async function caricaRecap() {
    const dataInizio = document.getElementById("dataPicker").value;
    const container = document.getElementById("recapContainer");
    
    try {
        const response = await fetch(`ajaxOperatoreMissioni.php?startDate=${dataInizio}`);
        const html = await response.text();
        container.innerHTML = html;
    } catch (error) {
        container.innerHTML = `<div class="alert alert-danger">Errore nel caricamento dei dati.</div>`;
    }
}
</script>
</body>
</html>