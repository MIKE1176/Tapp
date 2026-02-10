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
                onclick="document.querySelector('#modalCreaTurno form').action='creaTurno.php'; 
                        document.getElementById('idTurnoModifica')?.remove(); 
                        document.querySelector('#modalCreaTurno .modal-title').innerText='Nuovo Turno';" 
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
    // Reset totale dell'orario per evitare scivolamenti di data
    oggi.setHours(0, 0, 0, 0); 
    
    let giornoSettimana = oggi.getDay(); // 0 è Domenica, 1 è Lunedì...
    
    // Calcoliamo la distanza dal Lunedì (se oggi è domenica/0, diff è 6)
    const diff = (giornoSettimana === 0) ? 6 : giornoSettimana - 1;
    
    const lunediCorrente = new Date(oggi);
    lunediCorrente.setDate(oggi.getDate() - diff);
    
    // Formattazione manuale YYYY-MM-DD per l'input date
    const yyyy = lunediCorrente.getFullYear();
    const mm = String(lunediCorrente.getMonth() + 1).padStart(2, '0');
    const dd = String(lunediCorrente.getDate()).padStart(2, '0');
    const dataFinale = `${yyyy}-${mm}-${dd}`;
    
    document.getElementById("data").value = dataFinale;
    
    await caricaConfig(); 
    caricaSettimana();
}

function aggiornaGraficaSlot() {
    const tuttiIBox = grid.querySelectorAll('.slot-row');
    tuttiIBox.forEach(box => {
        const cb = box.querySelector('input');
        if (cb.checked) {
            box.classList.add('active-border');
        } else {
            box.classList.remove('active-border');
        }
    });
}

function spostaSettimana(giorni){
    const d = new Date(settimanaStart);
    d.setDate(d.getDate() + giorni);
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    document.getElementById("data").value = `${y}-${m}-${dd}`;
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

function caricaSettimana() {
    const dataSel = document.getElementById("data").value;
    if(!dataSel) return;
    settimanaStart = new Date(dataSel);
    const cont = document.getElementById("contenitoreSettimana");
    const selectGiorno = document.getElementById("giornoTurno");
    
    cont.innerHTML = "";
    selectGiorno.innerHTML = "";

    for (let i = 0; i < 7; i++) {
        const giorno = new Date(settimanaStart);
        giorno.setDate(settimanaStart.getDate() + i);
        const y = giorno.getFullYear();
        const m = String(giorno.getMonth() + 1).padStart(2, '0');
        const d = String(giorno.getDate()).padStart(2, '0');
        const iso = `${y}-${m}-${d}`;
        
        let dayNum = giorno.getDay(); 
        if (dayNum === 0) dayNum = 7;

        const giornoAttivo = configOrari ? configOrari.giorni_attivi.includes(dayNum) : true;
        let titolo = giorno.toLocaleDateString("it-IT", {weekday:"long", day:"2-digit", month:"2-digit"});
        titolo = titolo.toLowerCase().replace(/(?:^|[\s\-\/\(])\S/g, function(a) { return a.toUpperCase(); });

        if (giornoAttivo) {
            selectGiorno.innerHTML += `<option value="${iso}">${titolo}</option>`;
        }

        const col = document.createElement("div");
        col.className = "col-12 col-md";
        col.innerHTML = `
            <div class="giorno-box ${giornoAttivo ? '' : 'bg-light opacity-75'}">
                <div class="titolo-giorno border-bottom pb-1 mb-2 text-center">${titolo}</div>
                <div id="g_${iso}" class="mt-3 text-center">
                    ${giornoAttivo ? 'Caricamento...' : '<h5 class="text-muted mt-4 px-2" style="font-size: 1rem;">Servizio non attivo</h5>'}
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
        
        // Colleghiamo l'evento onchange
        document.getElementById("giornoTurno").addEventListener('change', generaSlot);
    }
    // Chiamiamo generaSlot solo se abbiamo già una data nel select
    if (document.getElementById("giornoTurno").value) {
        generaSlot();
    }
}

function formattaOraFascia(valore) {
    let h = Math.floor(valore);
    let m = ((valore % 1) === 0.5 || (valore % 1) === 0.1) ? "30" : "00"; 
    if(valore % 1 > 0 && valore % 1 < 0.2) m = "10"; 
    return String(h).padStart(2, "0") + ":" + m;
}

// Aggiungiamo i parametri opzionali per la pre-selezione
async function generaSlot(preSelectInizio = null, preSelectFine = null) {
    if (!configOrari) return;
    grid.innerHTML = "";
    selezionati.clear();
    
    const dataSelezionata = document.getElementById("giornoTurno").value;
    const btnSalva = document.querySelector('#modalCreaTurno button[type="submit"]');

    if (!dataSelezionata) return;

    const d = new Date(dataSelezionata);
    d.setHours(12, 0, 0, 0); 
    let dayNum = d.getDay(); 
    if (dayNum === 0) dayNum = 7;

    if (!configOrari.giorni_attivi.includes(dayNum)) {
        grid.innerHTML = `<div class="col-12"><div class="alert alert-warning py-2 text-center">Servizio non attivo.</div></div>`;
        if(btnSalva) btnSalva.disabled = true;
        return;
    }

    if (!verificaPreavviso(dataSelezionata)) {
        grid.innerHTML = `<div class="col-12"><div class="alert alert-danger small py-2 text-center">Preavviso scaduto!</div></div>`;
        if(btnSalva) btnSalva.disabled = true;
        return;
    }
    
    if(btnSalva) btnSalva.disabled = false;

    const fasciaSel = document.getElementById("fascia").value;
    const orariFascia = configOrari.orari[fasciaSel];
    const stepOra = configOrari.slot_turni / 60;

    for (let oraCorrente = orariFascia.inizio; oraCorrente < orariFascia.fine; oraCorrente += stepOra) {
        const inizioString = formattaOraFascia(oraCorrente);
        const fineString = formattaOraFascia(oraCorrente + stepOra);

        let isChecked = true; 
        if (preSelectInizio && preSelectFine) {
            isChecked = (inizioString >= preSelectInizio && inizioString < preSelectFine);
        }

        const row = document.createElement("div");
        row.className = "col-12";
        const item = document.createElement("div");
        item.className = "slot-row" + (isChecked ? " active-border" : "");
        item.innerHTML = `
            <span class="small fw-bold">${inizioString} → ${fineString}</span>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="sw_${inizioString}" ${isChecked ? 'checked' : ''}>
            </div>
        `;

        const checkbox = item.querySelector('input');
        if (isChecked) selezionati.add(inizioString); 

        checkbox.onchange = () => {
            const inputs = Array.from(grid.querySelectorAll('input[type="checkbox"]'));
            const checkedInputs = inputs.filter(i => i.checked);
            
            let valida = true;
            if (checkedInputs.length > 0) {
                const primoIdx = inputs.indexOf(checkedInputs[0]);
                const ultimoIdx = inputs.indexOf(checkedInputs[checkedInputs.length - 1]);
                // Se la differenza tra gli indici non corrisponde al numero di check, c'è un buco
                if ((ultimoIdx - primoIdx + 1) !== checkedInputs.length) valida = false;
            } else {
                valida = false; // Almeno uno deve essere selezionato
            }

            if (!valida) {
                // Annulla il click
                checkbox.checked = !checkbox.checked;
                
                // --- LOGICA ALERT (RIVISTA) ---
                if (!document.getElementById("temp-alert-box")) {
                    const alertDiv = document.createElement("div");
                    alertDiv.id = "temp-alert-box";
                    alertDiv.className = "col-12 order-last"; // Lo mettiamo in fondo
                    alertDiv.innerHTML = `
                        <div class="alert alert-warning p-2 mt-2 small border-0 text-center shadow-sm" 
                             style="background-color: #fff3cd; color: #856404; font-weight: 600;">
                            ⚠️ Selezione continua richiesta
                        </div>`;
                    grid.appendChild(alertDiv);
                    setTimeout(() => {
                        const el = document.getElementById("temp-alert-box");
                        if(el) el.remove();
                    }, 2500);
                }
            } else {
                // Aggiornamento Set e Grafica
                if (checkbox.checked) {
                    selezionati.add(inizioString);
                    item.classList.add('active-border');
                } else {
                    selezionati.delete(inizioString);
                    item.classList.remove('active-border');
                }
            }
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

function apriModificaTurno(id, data, inizio, fine) {
    // 1. Cambia il titolo e l'action del form
    const modalElement = document.getElementById('modalCreaTurno');
    modalElement.querySelector('.modal-title').innerText = "Modifica Turno #" + id;
    const form = modalElement.querySelector('form');
    form.action = "modificaTurno.php";

    // 2. Gestisci l'input ID del turno (crealo se non esiste)
    let inputId = document.getElementById('idTurnoModifica');
    if(!inputId) {
        inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'idTurno';
        inputId.id = 'idTurnoModifica';
        form.appendChild(inputId);
    }
    inputId.value = id;

    // 3. Imposta la data nel select
    document.getElementById('giornoTurno').value = data;

    // 4. Mostra il modal
    const bsModal = new bootstrap.Modal(modalElement);
    bsModal.show();

    // 5. Rigenera gli slot (passando gli orari attuali per pre-selezionarli)
    // Passiamo inizio e fine per far capire a generaSlot cosa spuntare
    generaSlot(inizio, fine);
}

function apriModalElimina(id) {
    var myModal = new bootstrap.Modal(document.getElementById('modalDelete' + id));
    myModal.show();
}
</script>

</body>
</html>