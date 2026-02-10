<?php
include("./session.php");

if(!isset($_GET['date'])){
    exit;
}

$id = (int)$_SESSION['ID'];

$dataRaw = trim($_GET['date']);

$dataObj =
   DateTime::createFromFormat('Ymd',$dataRaw)
   ?: DateTime::createFromFormat('Y-m-d',$dataRaw);

if(!$dataObj){
   exit("Formato data errato");
}

$data = $dataObj->format('Y-m-d');

$res = mysqli_query($db, "
SELECT
    t.ID,
    t.dataInizio,
    t.dataFine,
    t.note,
    a.codiceMezzo
FROM turno t
JOIN automezzo a ON t.automezzo = a.targa
WHERE t.id_operatore = $id
AND DATE(t.dataInizio) = '$data'
ORDER BY t.dataInizio ASC
");

if(mysqli_num_rows($res)==0){
    echo "
    <div class='col-12 text-center mt-3'>
      <h5>Nessun turno trovato.</h5>
    </div>";
    exit;
}

while($r = mysqli_fetch_assoc($res)){

    $inizio = date("H:i", strtotime($r['dataInizio']));
    $fine   = date("H:i", strtotime($r['dataFine']));
    $idTurno = $r['ID'];

    echo "
    <div class='col-12 col-md-6 my-3'>
      <div class='card shadow p-3' style='min-width: 420px'>

        <h4>{$r['codiceMezzo']}</h4>

        <p>
          <strong>Orario:</strong> $inizio → $fine
        </p>

        <p class='text-muted'>
          {$r['note']}
        </p>

        <div class='d-flex justify-content-end'>

          <button class='btn btn-outline-danger'
                  data-bs-toggle='modal'
                  data-bs-target='#modalDelete$idTurno'>
            Elimina
          </button>

        </div>
      </div>
    </div>

    <div class='modal fade' id='modalDelete$idTurno' tabindex='-1'>
      <div class='modal-dialog'>
        <div class='modal-content'>

          <div class='modal-header'>
            <h5 class='modal-title'>Elimina turno</h5>
            <button class='btn-close' data-bs-dismiss='modal'></button>
          </div>

          <div class='modal-body'>
            Sei sicuro di voler eliminare questo turno?
          </div>

          <div class='modal-footer'>
            <button class='btn btn-secondary'
                    data-bs-dismiss='modal'>
              Annulla
            </button>

            <form method='post' action='eliminaTurno.php'>
              <input type='hidden'
                     name='idTurno'
                     value='$idTurno'>
              <button class='btn btn-danger'>
                Elimina
              </button>
            </form>

          </div>

        </div>
      </div>
    </div>";
}
