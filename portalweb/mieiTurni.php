<?php
include('./session.php');
?>

<!DOCTYPE html>
<html lang="it">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<link rel="icon" href="./assets/favicon.ico">
<title>Misegello - I tuoi turni</title>

<style>
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
</style>

</head>

<body onload="initPagina()">

<?php include('./navbar.php'); ?>

<div class="container-fluid">

<!-- CONTROLLI SETTIMANA -->

<div class="row align-items-end my-3">

<div class="col-3 col-md-2">
<button class="btn btn-outline-secondary w-100"
        onclick="spostaSettimana(-7)">
←
</button>
</div>

<div class="col-6 col-md-4">
<label>Settimana</label>
<input type="date"
       id="data"
       class="form-control"
       onchange="caricaSettimana()">
</div>

<div class="col-3 col-md-2">
<button class="btn btn-outline-secondary w-100"
        onclick="spostaSettimana(7)">
→
</button>
</div>

<div class="col-md-4 d-none d-md-block text-end">
<button class="btn btn-success"
        data-bs-toggle="modal"
        data-bs-target="#modalCreaTurno">
Crea turno
</button>
</div>

<div class="col-12 d-md-none mt-2">
<button class="btn btn-success w-100"
        data-bs-toggle="modal"
        data-bs-target="#modalCreaTurno">
Crea turno
</button>
</div>

</div>

<!-- SETTIMANA -->

<div id="contenitoreSettimana"
     class="row g-3"></div>
</div>

</div>

<!-- ================= MODAL CREA TURNO ================= -->

<div class="modal fade" id="modalCreaTurno">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Nuovo turno</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form action="creaTurno.php" method="post">

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
<option value="mattina">Mattina (08:30–13:30)</option>
<option value="pomeriggio">Pomeriggio (14:30–19:30)</option>
</select>
</div>

<div id="slotGrid" class="row g-2"></div>

<input type="hidden"
       name="slotSelezionati"
       id="slotSelezionati">

</div>

<div class="modal-footer">
<button class="btn btn-secondary"
        data-bs-dismiss="modal">
Annulla
</button>

<button class="btn btn-success">
Salva
</button>
</div>

</form>

</div>
</div>
</div>

<script>

let settimanaStart;

/* ===== Init ===== */

function initPagina(){
  const oggi = new Date().toISOString().split("T")[0];
  document.getElementById("data").value = oggi;
  caricaSettimana();
}


/* ===== frecce settimana ===== */

function spostaSettimana(giorni){
  const d = new Date(settimanaStart);
  d.setDate(d.getDate()+giorni);

  document.getElementById("data").value =
    d.toISOString().split("T")[0];

  caricaSettimana();
}

/* ===== carica settimana ===== */
function caricaSettimana(){

  const dataSel = document.getElementById("data").value;

  // ora la settimana parte dal giorno scelto
  settimanaStart = new Date(dataSel);

  const cont = document.getElementById("contenitoreSettimana");
  const selectGiorno = document.getElementById("giornoTurno");

  cont.innerHTML="";
  selectGiorno.innerHTML="";

  for(let i=0;i<7;i++){

    const giorno = new Date(settimanaStart);
    giorno.setDate(settimanaStart.getDate()+i);

    const iso = giorno.toISOString().split("T")[0];

    const titolo = giorno.toLocaleDateString(
      "it-IT",
      {weekday:"long", day:"2-digit", month:"2-digit"}
    );

    selectGiorno.innerHTML +=
      `<option value="${iso}">
        ${titolo}
       </option>`;

    const col = document.createElement("div");
    col.className="col-12 col-md";

    col.innerHTML=`
      <div class="giorno-box">
        <div class="titolo-giorno text-capitalize">
          ${titolo}
        </div>
        <div id="g_${iso}">
          Caricamento...
        </div>
      </div>
    `;

    cont.appendChild(col);

    fetch("cercaMieiTurni.php?date="+iso)
      .then(r=>r.text())
      .then(html=>{
        document.getElementById("g_"+iso).innerHTML = html;
      });
  }
}


/* ===== SLOT ===== */

const grid = document.getElementById("slotGrid");
let selezionati = new Set();

function generaSlot(){

  grid.innerHTML="";
  selezionati.clear();

  let start =
    document.getElementById("fascia").value==="mattina"
    ? 8.5 : 14.5;

  for(let i=0;i<5;i++){

    let hour=Math.floor(start+i);
    let minute=((start+i)%1)?"30":"00";

    const ora =
      String(hour).padStart(2,"0")+":"+minute;

    const col=document.createElement("div");
    col.className="col-4";

    const div=document.createElement("div");
    div.className="border rounded p-2 text-center slot selected";
    div.textContent=ora;

    selezionati.add(ora);

    div.onclick=()=>{
      div.classList.toggle("selected");

      if(selezionati.has(ora))
        selezionati.delete(ora);
      else
        selezionati.add(ora);

      document.getElementById("slotSelezionati").value =
        Array.from(selezionati).join(",");
    };

    col.appendChild(div);
    grid.appendChild(col);
  }

  document.getElementById("slotSelezionati").value =
    Array.from(selezionati).join(",");
}

generaSlot();

</script>

</body>
</html>
