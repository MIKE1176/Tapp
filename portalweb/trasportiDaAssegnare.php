<?php
  include("session.php");
  check_auth(); // Se non loggato o non attivo, scappa e va al login
  $idOperatore = $_SESSION['ID'];
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <title>Tapp - Gestione Luoghi</title>
    <style>
        .mission-card {
            transition: all 0.2s ease;
            border-left: 5px solid #0d6efd;
            border-radius: 15px;
        }
        .mission-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        }
        /* Bottoni sistemati per il centraggio */
        .btn-action {
            height: 60px; /* Altezza fissa per allineamento */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.75rem;
        }
        .btn-action i {
            font-size: 1.2rem;
            margin-bottom: 2px;
        }
        .giorno-box {
            min-height: 100px;
            transition: min-height 0.3s ease;
        }
        .giorno-box.has-content {
            min-height: 250px;
        }
        .giorno-inattivo {
            background-color: #f1f3f5;
            opacity: 0.8;
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
          const currentNav = document.getElementById("trasportiDaAssegnare");
          if(currentNav) currentNav.classList.add("fw-bold");
      });
  </script>

  <div class="container-fluid">

    <div class="row my-4">
      <div class="col-12 text-center">
        <h2 class="fw-bold text-primary text-uppercase">Gestione Assegnazioni</h2>
      </div>
    </div>
    
    <div class="row justify-content-center g-2 mb-4">
      <div class="col-2 col-md-1 d-flex">
        <button class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center shadow-none" onclick="spostaSettimana(-7)">
          <i class="bi bi-chevron-left"></i>
        </button>
      </div>

      <div class="col-8 col-md-4">
        <input type="date" id="data" class="form-control form-control-lg text-center fw-bold border-primary" onchange="caricaSettimana()">
      </div>

      <div class="col-2 col-md-1 d-flex">
        <button class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center shadow-none" onclick="spostaSettimana(7)">
          <i class="bi bi-chevron-right"></i>
        </button>
      </div>
    </div>

    <div id="contenitoreSettimana" class="row g-3 px-2"></div>

  </div>

<script>
let settimanaStart;
let configOrari = null;

async function initPagina(){
    try {
        const response = await fetch('../config_orari.json');
        configOrari = await response.json();
    } catch (e) { console.error("Config non trovata"); }

    const oggi = new Date();
    document.getElementById("data").value = oggi.toISOString().split('T')[0];
    caricaSettimana();
}

function spostaSettimana(giorni){
    const d = new Date(document.getElementById("data").value);
    d.setDate(d.getDate() + giorni);
    document.getElementById("data").value = d.toISOString().split('T')[0];
    caricaSettimana();
}

function caricaSettimana() {
    const dataSel = document.getElementById("data").value;
    if(!dataSel) return;
    settimanaStart = new Date(dataSel);
    const cont = document.getElementById("contenitoreSettimana");
    cont.innerHTML = ""; 

    for (let i = 0; i < 7; i++) {
        const giorno = new Date(settimanaStart);
        giorno.setDate(settimanaStart.getDate() + i);
        const iso = giorno.toISOString().split('T')[0];
        
        let jsDay = giorno.getDay();
        let jsonDay = (jsDay === 0) ? 7 : jsDay; 
        const isAttivo = configOrari?.giorni_attivi.includes(jsonDay);

        let titolo = giorno.toLocaleDateString("it-IT", {weekday:"short", day:"2-digit", month:"2-digit"});

        const col = document.createElement("div");
        col.className = "col-12 col-md"; 
        
        let htmlGiorno = "";
        if (isAttivo) {
            htmlGiorno = `
                <div class="h-100 border rounded-4 shadow-sm overflow-hidden bg-white giorno-box" id="box_${iso}">
                    <div class="p-2 text-center bg-primary text-white fw-bold text-uppercase small">${titolo}</div>
                    <div id="g_${iso}" class="p-2 d-flex flex-column gap-2">
                        <div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></div>
                    </div>
                </div>`;
        } else {
            htmlGiorno = `
                <div class="h-100 border rounded-4 giorno-inattivo giorno-box d-flex flex-column">
                    <div class="p-2 text-center bg-secondary text-white fw-bold text-uppercase small opacity-50">${titolo}</div>
                    <div class="flex-grow-1 d-flex align-items-center justify-content-center p-3">
                        <span class="text-secondary fw-bold small text-center text-uppercase">Servizio<br>Non Attivo</span>
                    </div>
                </div>`;
        }

        col.innerHTML = htmlGiorno;
        cont.appendChild(col);

        if (isAttivo) {
            fetch("cercaTrasportiDaAssegnare.php?date=" + iso)
                .then(r => r.text())
                .then(html => {
                    const gCont = document.getElementById("g_" + iso);
                    const bCont = document.getElementById("box_" + iso);
                    if(html.includes('mission-card')) {
                        bCont.classList.add('has-content');
                        gCont.innerHTML = html;
                    } else {
                        gCont.innerHTML = `<div class="text-center py-4 text-muted small fw-bold text-uppercase">Nessuna<br>Missione</div>`;
                    }
                });
        }
    }
}

// Funzione invio dati (rimasta uguale ma con feedback)
function gestisciMissione(id, azione) {
    const notaExtra = document.getElementById('nota_' + id).value;
    if(!confirm(azione === 'accetta' ? "Accetti la missione?" : "Confermi la non disponibilità?")) return;
    
    const formData = new FormData();
    formData.append('id_missione', id);
    formData.append('azione', azione); // 'accetta' o 'rifiuta'
    formData.append('nota_extra', notaExtra);

    fetch('aggiornaStatoTrasporto.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if(data.success) caricaSettimana();
        else alert(data.message);
    });
}
</script>
</body>
</html>