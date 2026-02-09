<?php
include("./session.php");

$mezzo   = $_POST['mezzo'];
$autista = (int)$_POST['autista'];

$slotRaw = $_POST['slotSelezionati'] ?? '';
$data    = $_POST['dataTurno']; // data selezionata nella pagina

if(!$slotRaw || !$data){
    header("location: mieiTurni.php");
    exit;
}

$slots = explode(",", $slotRaw);
sort($slots);

/*
    Convertiamo in minuti per facilitarci i calcoli
*/
$minuti = [];

foreach($slots as $s){
    list($h,$m) = explode(":", $s);
    $minuti[] = $h*60 + $m;
}

$blocchi = [];
$start = $minuti[0];
$prev  = $start;

for($i=1; $i<count($minuti); $i++){

    // se NON consecutivo (+30 min)
    if($minuti[$i] != $prev + 30){
        $blocchi[] = [$start, $prev];
        $start = $minuti[$i];
    }

    $prev = $minuti[$i];
}

$blocchi[] = [$start, $prev];

/*
    Inseriamo i turni
*/
foreach($blocchi as $b){

    $inizioMin = $b[0];
    $fineMin   = $b[1] + 30; // fine ultimo slot

    $inizio = sprintf(
        "%s %02d:%02d:00",
        $data,
        floor($inizioMin/60),
        $inizioMin%60
    );

    $fine = sprintf(
        "%s %02d:%02d:00",
        $data,
        floor($fineMin/60),
        $fineMin%60
    );

    mysqli_query($db,"
        INSERT INTO turno
        (dataInizio,dataFine,automezzo,id_operatore)
        VALUES('$inizio','$fine','$mezzo',$autista)
    ");
}

header("location: mieiTurni.php");
