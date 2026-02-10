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

// Carichiamo il preavviso dal JSON per il controllo lato server
$config = json_decode(file_get_contents('../config_orari.json'), true);
$giorniPreavviso = $config['preavviso_turno'] ?? 0;

// Calcolo se il turno è modificabile
$oggi = new DateTime();
$oggi->setTime(0,0,0);
$dataLimite = clone $oggi;
$dataLimite->modify("+$giorniPreavviso days");

$isModificabile = ($dataObj >= $dataLimite);

$res = mysqli_query($db, "
SELECT
    t.ID,
    t.dataInizio,
    t.dataFine,
    t.note,
    a.codiceMezzo
FROM turno t
LEFT JOIN automezzo a ON t.automezzo = a.targa
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
    $mezzo = $r['codiceMezzo'] ?? "Da assegnare";

    // Nota: Ho rimosso le classi 'col-12' e 'my-3' esterne per non rompere il layout
    echo "
    <div class='card mb-2 shadow-sm border-start border-4 border-success'>
      <div class='card-body p-2'>
        <div class='row align-items-center g-0'>
          
          <div class='col text-start'>
            <h6 class='card-title mb-0' style='font-size: 0.9rem;'>$mezzo</h6>
            <p class='card-text mb-0 fw-bold text-secondary' style='font-size: 0.85rem;'>$inizio - $fine</p>
          </div>";

    // Mostra i bottoni SOLO se è rispettato il preavviso
    if ($isModificabile) {
        echo "
          <div class='col-auto d-flex flex-column gap-1'>
            <button class='btn btn-primary btn-sm py-1' 
                    onclick='apriModificaTurno($idTurno, \"$data\", \"$inizio\", \"$fine\")'>
              Modifica
            </button>
            <button class='btn btn-outline-danger btn-sm py-1' 
                    onclick='apriModalElimina($idTurno)'>
              Elimina
            </button>
          </div>";
    } else {
        echo "
          <div class='col-auto'>
            <span class='badge bg-light text-muted border'>Bloccato</span>
          </div>";
    }

    echo "
        </div>
      </div>
    </div>";

    // Modal elimina (lo stampiamo solo se serve)
    if ($isModificabile) {
        echo "
        <div class='modal fade' id='modalDelete$idTurno' tabindex='-1' aria-hidden='true'>
          <div class='modal-dialog modal-sm modal-dialog-centered'>
            <div class='modal-content text-center p-3'>
                <p class='mb-3 fw-bold'>Eliminare il turno?</p>
                <div class='d-flex justify-content-center gap-2'>
                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>No</button>
                    <form method='post' action='eliminaTurno.php'>
                        <input type='hidden' name='idTurno' value='$idTurno'>
                        <button type='submit' class='btn btn-danger'>Sì</button>
                    </form>
                </div>
            </div>
          </div>
        </div>";
    }
}
?>
