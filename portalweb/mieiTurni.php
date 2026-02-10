<?php
  include("session.php");
  check_auth();
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
  <title>Tapp - I miei Turni</title>

  <style>
    .btn-outline-secondary {
        --bs-btn-hover-bg: transparent !important;
        --bs-btn-hover-color: #6c757d !important;
        --bs-btn-active-bg: transparent !important;
        --bs-btn-active-color: #6c757d !important;
        --bs-btn-focus-shadow-rgb: none !important;
    }

    .btn-outline-secondary:focus, 
    .btn-outline-secondary:active, 
    .btn-outline-secondary:hover {
        background-color: transparent !important;
        color: #6c757d !important;
        border-color: #6c757d !important;
        box-shadow: none !important;
        outline: none !important;
    }

    body.modal-open {
      padding-right: 0 !important;
    }

    #contenitoreSettimana {
      padding-bottom: 50px; /* Spazio in fondo alla pagina */
    }

    .giorno-box{
      border:1px solid #ddd;
      border-radius:12px;
      padding:10px;
      background:#fff;
      height:100%;
    }

    .titolo-giorno{
      font-weight:600;
      margin-bottom:10px;
    }

    .slot.selected{
      background:#198754;
      color:white;
    }

    .slot-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 4px 10px !important;
      margin-bottom: 3px !important;
      transition: border 0.2s;
    }

    /* Bordo verde se selezionato */
    .slot-row.active-border {
      border: 2px solid #198754 !important;
    }

    .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }
  </style>

</head>

<body onload="initPagina()">

  <?php
    include('./navbar.php');
    $idOperatore = $_SESSION['ID'];
  ?>
  <script>
      document.addEventListener("DOMContentLoaded", () => {
          const navLinks = document.querySelectorAll(".nav-link");
          navLinks.forEach(link => link.classList.remove("fw-bold"));
          const currentNav = document.getElementById("mieiTurni");
          if(currentNav) currentNav.classList.add("fw-bold");
      });
  </script>

  <div class="container-fluid">

    <div class="row my-3">
      <div class="col-12">
        <button class="btn btn-success btn-lg w-100" 
                data-bs-toggle="modal" 
                data-bs-target="#modalCreaTurno">
          Crea turno
        </button>
      </div>
    </div>

    <div class="row justify-content-center align-items-end my-3">
      
      <div class="col-3 col-md-2">
        <button class="btn btn-outline-secondary w-100 shadow-none" onclick="spostaSettimana(-7); this.blur();">
          ←
        </button>
      </div>

      <div class="col-6 col-md-4 text-center">
        <label for="data" class="form-label">Settimana</label>
        <input type="date" 
              id="data" 
              class="form-control" 
              onchange="caricaSettimana()">
      </div>

      <div class="col-3 col-md-2">
        <button class="btn btn-outline-secondary w-100 shadow-none" onclick="spostaSettimana(7); this.blur();">
          →
        </button>
      </div>

    </div>

    <div id="contenitoreSettimana"
        class="row g-3 px-3"></div>

  </div>

  <div class="modal fade" id="modalCreaTurno" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Nuovo Turno</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <form action="creaTurno.php" method="post">
          <input type="hidden" name="idOperatore" value="<?php echo $_SESSION['ID']; ?>">
          <div class="modal-body">

          <div class="mb-3">
            <label>Giorno</label>
            <select id="giornoTurno"
                    name="dataTurno"
                    class="form-select"
                    required>
            </select>
          </div>

          <div class="mb-3">
          <label>Fascia</label>
            <select id="fascia"
                    class="form-select"
                    onchange="generaSlot()">
              </select>
          </div>

          <div id="slotGrid" class="row g-1"></div>

            <input type="hidden"
                  name="slotSelezionati"
                  id="slotSelezionati">

          </div>

          <div class="modal-footer">
            <button type="button" 
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
            Annulla
            </button>

            <button type="submit" class="btn btn-success">
            Salva
            </button>
          </div>

        </form>

      </div>
    </div>
  </div>

<script>
let settimanaStart;
const grid = document.getElementById("slotGrid");
let selezionati = new Set();
let configOrari = null;

async function initPagina(){
    const oggi = new Date();
    
    // getDay() restituisce: 0 (Dom), 1 (Lun), 2 (Mar), 3 (Mer), 4 (Gio), 5 (Ven), 6 (Sab)
    const giornoSettimana = oggi.getDay(); 
    
    // Calcoliamo la differenza per arrivare al Lunedì
    // Se è domenica (0), dobbiamo tornare indietro di 6 giorni.
    // Se è lunedì (1), torniamo indietro di 0.
    // Se è martedì (2), torniamo indietro di 1, e così via.
    const diff = oggi.getDate() - (giornoSettimana === 0 ? 6 : giornoSettimana - 1);
    
    const lunediCorrente = new Date(oggi.setDate(diff));
    
    // Formattiamo la data in YYYY-MM-DD per l'input date
    const isoLunedi = lunediCorrente.toISOString().split("T")[0];
    
    document.getElementById("data").value = isoLunedi;
    
    // Carichiamo prima la config, poi la settimana
    await caricaConfig(); 
    caricaSettimana();
}

function spostaSettimana(giorni){
    const d = new Date(settimanaStart);
    d.setDate(d.getDate() + giorni);
    document.getElementById("data").value = d.toISOString().split("T")[0];
    if (document.activeElement) document.activeElement.blur();
    caricaSettimana();
}

function verificaPreavviso(dataScelta) {
    if(!configOrari) return true;
    const preavviso = configOrari.preavviso_turno || 0;
    const oggi = new Date();
    oggi.setHours(0,0,0,0);
    const limite = new Date(oggi);
    limite.setDate(oggi.getDate() + preavviso);
    const scelta = new Date(dataScelta);
    return scelta >= limite;
}

function apriModificaTurno(id, data, inizio, fine) {
    // Verifichiamo subito il preavviso anche per la modifica
    if (!verificaPreavviso(data)) {
        alert("Non puoi modificare questo turno. Preavviso minimo: " + configOrari.preavviso_turno + " giorni.");
        return;
    }

    // Cambiamo il titolo del modal e l'action della form
    const modal = document.getElementById('modalCreaTurno');
    modal.querySelector('.modal-title').innerText = "Modifica Turno #" + id;
    const form = modal.querySelector('form');
    form.action = "modificaTurno.php"; // Indirizziamo a un nuovo file

    // Aggiungiamo l'ID del turno alla form se non c'è già
    let inputId = document.getElementById('idTurnoModifica');
    if(!inputId) {
        inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'idTurno';
        inputId.id = 'idTurnoModifica';
        form.appendChild(inputId);
    }
    inputId.value = id;

    // Impostiamo la data nel select
    document.getElementById('giornoTurno').value = data;
    
    // Apriamo il modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    // Rigeneriamo gli slot (qui l'utente sceglierà i nuovi orari)
    generaSlot();
}

function caricaSettimana() {
    const dataSel = document.getElementById("data").value;
    settimanaStart = new Date(dataSel);
    const cont = document.getElementById("contenitoreSettimana");
    const selectGiorno = document.getElementById("giornoTurno");
    
    cont.innerHTML = "";
    selectGiorno.innerHTML = "";

    for (let i = 0; i < 7; i++) {
        const giorno = new Date(settimanaStart);
        giorno.setDate(settimanaStart.getDate() + i);
        const iso = giorno.toISOString().split("T")[0];
        
        // Otteniamo il numero del giorno (0=Dom, 1=Lun, ..., 6=Sab)
        // Trasformiamo 0 (Dom) in 7 per allinearci al tuo JSON (1=Lun, 7=Dom)
        let dayNum = giorno.getDay(); 
        if (dayNum === 0) dayNum = 7;

        const giornoAttivo = configOrari ? configOrari.giorni_attivi.includes(dayNum) : true;

        let titolo = giorno.toLocaleDateString("it-IT", {weekday:"long", day:"2-digit", month:"2-digit"});
        titolo = titolo.toLowerCase().replace(/(?:^|[\s\-\/\(])\S/g, function(a) { return a.toUpperCase(); });

        // Aggiungiamo al select del Modal solo se il giorno è attivo
        if (giornoAttivo) {
            selectGiorno.innerHTML += `<option value="${iso}">${titolo}</option>`;
        }

        const col = document.createElement("div");
        col.className = "col-12 col-md";
        // Se il giorno non è attivo, lo rendiamo visivamente spento nella griglia principale
        const opacity = giornoAttivo ? "" : "opacity-50";
        const bg = giornoAttivo ? "" : "background:#f1f1f1;";

        col.innerHTML = `
            <div class="giorno-box ${giornoAttivo ? '' : 'bg-light opacity-75'}">
                <div class="titolo-giorno border-bottom pb-1 mb-2 text-center">${titolo}</div>
                <div id="g_${iso}" class="mt-3 text-center">
                    ${giornoAttivo ? 'Caricamento...' : '<h5 class="text-muted mt-2 px-2">Servizio non attivo</h5>'}
                </div>
            </div>
        `;
        cont.appendChild(col);

        if (giornoAttivo) {
            fetch("cercaMieiTurni.php?date=" + iso)
                .then(r => r.text())
                .then(html => {
                    document.getElementById("g_" + iso).innerHTML = html;
                });
        }
    }
}

async function caricaConfig() {
    if (!configOrari) {
        const response = await fetch('../config_orari.json');
        configOrari = await response.json();
        
        const fasciaSelect = document.getElementById("fascia");
        fasciaSelect.innerHTML = "";
        for (const [key, val] of Object.entries(configOrari.orari)) {
            const labelInizio = formattaOraFascia(val.inizio);
            const labelFine = formattaOraFascia(val.fine);
            const nomeFascia = key.charAt(0).toUpperCase() + key.slice(1);
            fasciaSelect.innerHTML += `<option value="${key}">${nomeFascia} (${labelInizio}–${labelFine})</option>`;
        }
        document.getElementById("giornoTurno").onchange = generaSlot;
    }
    generaSlot();
}

function formattaOraFascia(valore) {
    let h = Math.floor(valore);
    let m = ((valore % 1) === 0.5) ? "30" : "00";
    return String(h).padStart(2, "0") + ":" + m;
}

async function generaSlot() {
    if (!configOrari) return;

    grid.innerHTML = "";
    selezionati.clear();
    
    const dataSelezionata = document.getElementById("giornoTurno").value;
    const dataObj = new Date(dataSelezionata);
    let dayNum = dataObj.getDay();
    if (dayNum === 0) dayNum = 7;

    const btnSalva = document.querySelector('#modalCreaTurno button[type="submit"]');

    // 1. Controllo Giorno Attivo
    if (!configOrari.giorni_attivi.includes(dayNum)) {
        grid.innerHTML = `<div class="col-12"><div class="alert alert-warning">Il servizio non è attivo in questo giorno.</div></div>`;
        if(btnSalva) btnSalva.disabled = true;
        return;
    }

    // 2. Controllo Preavviso (usando la nuova variabile)
    if (!verificaPreavviso(dataSelezionata)) {
        grid.innerHTML = `<div class="col-12"><div class="alert alert-danger"><b>Attenzione:</b> Preavviso richiesto di ${configOrari.preavviso_turno} giorni!</div></div>`;
        if(btnSalva) btnSalva.disabled = true;
        return;
    }

    if(btnSalva) btnSalva.disabled = false;

    // Generazione slot basata su configOrari.orari
    const fasciaSel = document.getElementById("fascia").value;
    const orariFascia = configOrari.orari[fasciaSel]; // Usiamo la chiave "orari" dal JSON
    const minutiSlot = configOrari.slot_turni; 
    const stepOra = minutiSlot / 60;

    // Titolo istruzioni
    const info = document.createElement("div");
    info.className = "col-12 mb-2";
    info.innerHTML = `<small class="text-muted">Trascina o clicca per selezionare gli orari:</small>`;
    grid.appendChild(info);

    for (let oraCorrente = orariFascia.inizio; oraCorrente < orariFascia.fine; oraCorrente += stepOra) {
        const inizioString = formattaOraFascia(oraCorrente);
        const fineString = formattaOraFascia(oraCorrente + stepOra);

        const row = document.createElement("div");
        row.className = "col-12";
        
        const item = document.createElement("div");
        item.className = "slot-row active-border";
        item.innerHTML = `
            <span class="small fw-bold">${inizioString} → ${fineString}</span>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="sw_${inizioString}" checked>
            </div>
        `;

        const checkbox = item.querySelector('input');
        selezionati.add(inizioString); 

        checkbox.onchange = () => {
            // ... logica di validazione continuità (rimane uguale a prima) ...
            validazioneContinuita(checkbox, item, inizioString);
            aggiornaCampoNascosto();
        };

        row.appendChild(item);
        grid.appendChild(row);
    }
    aggiornaCampoNascosto();
}

function aggiornaCampoNascosto() {
    document.getElementById("slotSelezionati").value = Array.from(selezionati).sort().join(",");
}

function apriModalElimina(id) {
    var myModal = new bootstrap.Modal(document.getElementById('modalDelete' + id));
    myModal.show();
}

initPagina();
</script>

</body>
</html>