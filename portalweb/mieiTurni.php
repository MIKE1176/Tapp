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
/* ===== SLOT GRID ===== */

.slot-grid{
  display:grid;
  grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
  gap:10px;
  max-height:320px;
  overflow:auto;
}

.slot{
  border:1px solid #ccc;
  border-radius:10px;
  padding:10px 0;
  text-align:center;
  cursor:pointer;
  user-select:none;
  font-weight:500;
  background:#f8f9fa;
  transition: all .15s ease;
}

.slot:hover{
  background:#e9ecef;
}

.slot.selected{
  background:#198754;
  color:white;
  border-color:#198754;
}
</style>

</head>

<body onload="showHint(null)">

<?php include('./navbar.php'); ?>

<script>
document.querySelectorAll(".nav-link").forEach(el=>{
  el.classList.remove("fw-bold");
});
document.getElementById("mieiTurni")?.classList.add("fw-bold");
</script>

<div class="container">

  <!-- ===== SELEZIONE DATA ===== -->

  <div class="row justify-content-center my-4">

    <div class="col-md-6 text-center">
      <label class="form-label">Seleziona data</label>
      <input type="date" id="data" class="form-control"
             onchange="showHint(this.value)">
    </div>

    <div class="col-md-3 d-flex align-items-end">
      <button class="btn btn-outline-success w-100"
              data-bs-toggle="modal"
              data-bs-target="#modalCreaTurno">
        Crea turno
      </button>
    </div>

  </div>

  <div class="row justify-content-center" id="riga"></div>

</div>

<!-- ================= MODAL CREA TURNO ================= -->

<div class="modal fade" id="modalCreaTurno" tabindex="-1">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Nuovo turno</h5>
        <button type="button" class="btn-close"
                data-bs-dismiss="modal"></button>
      </div>

      <form action="creaTurno.php" method="post">

        <div class="modal-body">

          <!-- AUTISTA -->

          <div class="mb-3">
            <label class="form-label">Autista</label>

            <select name="autista" class="form-select" required>
              <?php
              $res = mysqli_query(
                $db,
                "SELECT ID,nome,cognome
                 FROM operatore
                 WHERE attivo=1
                 ORDER BY cognome"
              );

              while ($r = mysqli_fetch_assoc($res)) {
                echo "<option value='{$r['ID']}'>
                        {$r['cognome']} {$r['nome']}
                      </option>";
              }
              ?>
            </select>
          </div>

          <!-- MEZZO -->

          <div class="mb-3">
            <label class="form-label">Mezzo</label>

            <select name="mezzo" class="form-select" required>
              <?php
              $mezzi = mysqli_query(
                $db,
                "SELECT targa,codiceMezzo
                 FROM automezzo
                 WHERE attivo=1
                 ORDER BY codiceMezzo"
              );

              while ($m = mysqli_fetch_assoc($mezzi)) {
                echo "<option value='{$m['targa']}'>
                        {$m['codiceMezzo']}
                      </option>";
              }
              ?>
            </select>
          </div>

          <!-- SLOT ORARI -->

          <div class="mb-3">
            <label class="form-label fw-bold">
              Orari (blocchi da 30 min)
            </label>

            <div id="slotGrid" class="slot-grid"></div>

            <input type="hidden" name="slotSelezionati" id="slotSelezionati">
            <input type="hidden" name="dataTurno" id="dataTurno">
          </div>

          <!-- NOTE -->

          <div class="mb-3">
            <label>Note</label>
            <textarea name="noteTurno"
                      class="form-control"
                      rows="3"></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button"
                  class="btn btn-outline-secondary"
                  data-bs-dismiss="modal">
            Annulla
          </button>

          <button type="submit"
                  class="btn btn-success">
            Salva
          </button>
        </div>

      </form>

    </div>

  </div>

</div>

<!-- ================= SCRIPT ================= -->

<script>
/* ===== Caricamento turni ===== */

function showHint(val){

  let data = val;

  if(!val){
    const oggi = new Date();
    data =
      oggi.getFullYear() + "-" +
      String(oggi.getMonth()+1).padStart(2,"0") + "-" +
      String(oggi.getDate()).padStart(2,"0");

    document.getElementById("data").value = data;
  }

  fetch("cercaMieiTurni.php?date="+data)
    .then(r=>r.text())
    .then(html=>{
      document.getElementById("riga").innerHTML = html;
    });
}

/* ===== Generazione slot ===== */

const grid = document.getElementById("slotGrid");
let selezionati = new Set();

function generaSlot(){
  for(let h=0; h<24; h++){
    for(let m of [0,30]){

      const ora =
        String(h).padStart(2,"0") + ":" +
        String(m).padStart(2,"0");

      const div = document.createElement("div");
      div.className = "slot";
      div.textContent = ora;

      div.onclick = () => {

        div.classList.toggle("selected");

        if(selezionati.has(ora))
          selezionati.delete(ora);
        else
          selezionati.add(ora);

        document.getElementById("slotSelezionati").value =
          Array.from(selezionati).sort().join(",");
      };

      grid.appendChild(div);
    }
  }
}

generaSlot();

/* ===== Reset slot quando chiudi modal ===== */

document.getElementById("modalCreaTurno")
.addEventListener("hidden.bs.modal", () => {

  selezionati.clear();

  document.querySelectorAll(".slot")
    .forEach(s => s.classList.remove("selected"));
});

/* ===== Imposta data turno quando apri modal ===== */

document.getElementById("modalCreaTurno")
.addEventListener("show.bs.modal", () => {

  document.getElementById("dataTurno").value =
    document.getElementById("data").value;
});
</script>

</body>
</html>
