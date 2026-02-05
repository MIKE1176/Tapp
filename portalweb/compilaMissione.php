<?php
include('./session.php');

// Recupero i dati dal form compilaMissione
$idMissione = $_POST['idMissioneCompilazione'];

//pagamento (se c'è)

$targa = $_POST['targaAutomezzo'];
$codiceMezzo = $_POST['codiceMezzo'];
$numeroVerbale = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM automezzo WHERE targa = '$targa';"))['numeroVerbale'];
$numeroVerbale = $numeroVerbale + 1;
$codiceMissione = $codiceMezzo . "/". $numeroVerbale;

$nRicevuta = $_POST['nRicevuta'];
$pagante = $_POST['paganteID'];
$ricevente = $_POST['ricevente'];
$riscosso = $_POST['pagato'];
if($riscosso == "True"){
    $riscosso = 1;
}elseif($riscosso == "False"){
    $riscosso = 0;
}

$importo = $_POST['importo'];
$tipo = $_POST['tipo'];
$per = $_POST['per'];

$noteMissione = $_POST['noteMissione'];

$partenza = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM evento WHERE id_missione = '$idMissione' and evento.tipo = 'PARTENZA';"));
$rientro = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM evento WHERE id_missione = '$idMissione' and evento.tipo = 'RIENTRO';"));

if(is_null($partenza) || is_null($rientro)){
    $_SESSION['errore'] = "passaggiNonSufficienti";
    header("location: missioni.php");
    mysqli_close($db);
}else{
    $partenza = $partenza['ID'];
    $rientro = $rientro['ID'];

    if($nRicevuta != ""){
        $query = "INSERT into pagamento(ricevuta,per,importo,riscosso,tipo,id_missione,id_volontario,id_paziente) values('$nRicevuta','$per','$importo','$riscosso','$tipo','$idMissione','$ricevente','$pagante');";
        $pagamento = mysqli_query($db, $query);
    }
    
    $query = "UPDATE missione SET missione.statoCompilazione = 'COMPILATA', missione.codiceMissione = '$codiceMissione', missione.noteMissione = '$noteMissione' WHERE ID = '$idMissione';";
    
    mysqli_query($db, "UPDATE automezzo SET automezzo.numeroVerbale = '$numeroVerbale' WHERE targa = '$targa';");
    
    $compilazione = mysqli_query($db, $query);
    mysqli_close($db);
    header("location: ./missioni.php");
}