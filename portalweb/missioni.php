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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
  .card-missione{ border-radius:18px }
  </style>

</head>

<body onload="caricaMissioni(null)">

<?php include("./navbar.php"); ?>

<div class="container-fluid px-3">
  <div class="row my-3">
    <div class="col-12">
      <label class="fw-bold">Giorno</label>
      <input type="date"
            class="form-control"
            onchange="caricaMissioni(this.value)">
    </div>
  </div>

<div class="row gy-3" id="contenitoreMissioni"></div>
  <div class="col-12 mb-3 text-end">
<button class="btn btn-success"
        onclick="accettaTutti()">
Accetta tutti
</button>
</div>
</div>

<script>

let dataCorrente=null;

function caricaMissioni(d){

 if(!d){
   let now=new Date();
   dataCorrente=now.toISOString().split("T")[0];
 }else{
   dataCorrente=d;
 }

 fetch("ajaxMissioniAutista.php?date="+dataCorrente)
   .then(r=>r.text())
   .then(html=>{
     document.getElementById("contenitoreMissioni").innerHTML=html;
   });
}

function assegnaMissione(id){

 if(!confirm("Confermi assegnazione missione?")) return;

 fetch("assegnaMissione.php",{
   method:"POST",
   headers:{'Content-Type':'application/x-www-form-urlencoded'},
   body:"id="+id
 })
 .then(()=>caricaMissioni(dataCorrente));
}

function accettaTutti(){

fetch("accettaTutti.php",{method:"POST"})
.then(()=>caricaServizi());
}

</script>

</body>
</html>
