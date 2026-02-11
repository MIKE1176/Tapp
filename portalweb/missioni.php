<?php
include("session.php");
check_auth();
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>TAPP - Trasporti</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<link rel="icon" type="image/x-icon" href="../assets/favicon.ico">

<style>
#settimanaContainer{
  padding-bottom:120px; /* spazio fondo mobile */
}

/* stile per i giorni vuoti in mobile */
.giorno-vuoto {
  height: 20px;
}

.giorno-col.giorno-vuoto {
    flex: none !important;
    overflow: hidden;
}

.giorni-row{
  display:flex;
  gap:12px;
  flex-wrap:wrap;
  align-items:flex-start;
}

.giorni-row {
  align-items: flex-start; /* già presente, ma non basta */
}

.giorno-col {
  width: 13.5%;
  align-self: flex-start; /* forza ogni card a mantenere la propria altezza */
}

.card-missione h5 {
    margin-bottom: 10px;
}

.card-missione .small {
    margin-bottom: 10px;
}

.card-missione p {
    margin: 10px 0;
}

.card-missione .btn {
    margin-top: 12px;
}


.missioni-container {
    text-align: left !important;
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding: 0;
    margin: 0;
}


.giorno-col{
  border:1px solid #ddd;
  border-radius:12px;
  padding:8px;
  background:#fff;
  height:auto;
  align-self:flex-start; /* <--- risolve il problema */
}

/* DESKTOP: altezza naturale */
.giorno-col.giorno-vuoto,
.giorno-col.giorno-disabilitato {
    min-height: 180px;
}

/* MOBILE: entrambi bassi */
@media (max-width: 768px) {

    .giorno-col.giorno-vuoto,
    .giorno-col.giorno-disabilitato {
        min-height: 80px;   /* altezza compatta */
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
}



.titolo-giorno{
  font-weight:600;
  margin-bottom:6px;
  border-bottom:1px solid #eee;
  padding-bottom:4px;
  text-align:center;
}

.giorno-col.giorno-disabilitato {
    background: #f8f9fa !important; /* grigio chiaro */
    border-color: #e0e0e0 !important;
    opacity: 0.75;
}

.giorno-col.giorno-disabilitato .titolo-giorno {
    background: transparent !important;
}


/* MOBILE */
@media (max-width:768px){
  .giorni-row{
    flex-direction:column;
  }

  .giorno-col.giorno-vuoto {
    flex: none !important;
    overflow: hidden;
}

  .giorno-col{
    margin-bottom: 6px;
  }

  .giorno-col{
    width:100%;
  }
}
</style>

</head>

<body onload="initPagina()">

<?php include("./navbar.php"); ?>

<div class="container-fluid px-3">

<!-- controlli -->
<div class="row my-3 align-items-end">

  <div class="col-3 col-md-2">
    <button class="btn btn-outline-secondary w-100"
            onclick="spostaSettimana(-5)">
      ←
    </button>
  </div>

  <div class="col-6 col-md-4">
    <label class="fw-bold">Giorno</label>
    <input type="date"
           id="dataPicker"
           class="form-control"
           onchange="setDataBase(this.value)">
  </div>

  <div class="col-3 col-md-2">
    <button class="btn btn-outline-secondary w-100"
            onclick="spostaSettimana(5)">
      →
    </button>
  </div>

</div>

<!-- settimana -->
<div id="settimanaContainer">
  <div id="giorniRow" class="giorni-row"></div>
</div>



</div>

<script>
let dataBase=null;

/* ======================
   UTILS
====================== */

function formatISO(d){
  return d.toISOString().split("T")[0];
}

function prossimoGiornoLavorativo(d){
  do{
    d.setDate(d.getDate()+1);
  }while(d.getDay() === 0 || d.getDay() === 6);
  return d;
}

/* ======================
   SETTIMANA
====================== */

function caricaSettimana() {

    const dataSel = document.getElementById("dataPicker").value;
    if (!dataSel) return;

    let settimanaStart = new Date(dataSel);
    settimanaStart.setHours(0,0,0,0);

    const cont = document.getElementById("giorniRow");
    cont.innerHTML = "";

    for (let i = 0; i < 7; i++) {

        const giorno = new Date(settimanaStart);
        giorno.setDate(settimanaStart.getDate() + i);

        const y = giorno.getFullYear();
        const m = String(giorno.getMonth() + 1).padStart(2, '0');
        const d = String(giorno.getDate()).padStart(2, '0');
        const iso = `${y}-${m}-${d}`;

        let dayNum = giorno.getDay();
        if (dayNum === 0) dayNum = 7; // domenica = 7

        const giornoAttivo = (dayNum >= 1 && dayNum <= 5);

        let titolo = giorno.toLocaleDateString("it-IT", {
            weekday: "long",
            day: "2-digit",
            month: "2-digit"
        });
        titolo = titolo.toLowerCase().replace(/(?:^|[\s\-\/\(])\S/g, a => a.toUpperCase());

        const col = document.createElement("div");
        col.className = "giorno-col";

        // GIORNO NON ATTIVO (sabato/domenica)
        if (!giornoAttivo) {
            col.classList.add("giorno-disabilitato"); 
            col.innerHTML = ` <div class="giorno-box">
                    <div class="titolo-giorno border-bottom pb-1 mb-2 text-center">${titolo}</div>
                    <div id="g_${iso}" class="mt-3 text-center">
                        <h5 class="text-muted mt-4 px-2" style="font-size: 1rem;">Servizio non attivo</h5>
                    </div>
                </div>
            `;
            cont.appendChild(col);
            continue;
        }

        // GIORNO ATTIVO (lun–ven)
        col.innerHTML = `
            <div class="titolo-giorno">${titolo}</div>
            <div id="g_${iso}" class="missioni-container small">Caricamento...</div>
        `;

        cont.appendChild(col);

        fetch("ajaxMissioniAutista.php?date=" + iso)
            .then(r => r.text())
            .then(html => {
                const box = document.getElementById("g_" + iso);

                if (html.trim() === "") {
    col.classList.add("giorno-vuoto");
    box.innerHTML = `
        <div class="text-center mt-4 px-2">
            <h5 class="text-muted" style="font-size: 1rem;">Nessuna missione</h5>
        </div>
    `;
}
 else {
                    box.innerHTML = html;
                    col.classList.remove("giorno-vuoto");
                }
            });
    }
}

/* ======================
   INIT
====================== */

function initPagina(){
  const oggi=new Date();
  oggi.setHours(0,0,0,0);

  dataBase=oggi;
  document.getElementById("dataPicker").value=formatISO(oggi);

  caricaSettimana();
}

/* ======================
   NAVIGAZIONE
====================== */

function setDataBase(val){
  dataBase=new Date(val);
  caricaSettimana();
}

function spostaSettimana(giorni){
  dataBase.setDate(dataBase.getDate()+giorni);
  document.getElementById("dataPicker").value=formatISO(dataBase);
  caricaSettimana();
}

/* ======================
   LOGICA ORIGINALE
====================== */

function assegnaMissione(id){
 if(!confirm("Confermi assegnazione missione?")) return;

 fetch("assegnaMissione.php",{
   method:"POST",
   headers:{'Content-Type':'application/x-www-form-urlencoded'},
   body:"id="+id
 }).then(()=>caricaSettimana());
}

function accettaTutti(){
 fetch("accettaTutti.php",{method:"POST"})
   .then(()=>caricaSettimana());
}

</script>

</body>
</html>
