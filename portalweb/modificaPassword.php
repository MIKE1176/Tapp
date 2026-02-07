<?php
include('./session.php');


$idVolontario = $_SESSION['ID_volontario'];

// Recupero i dati dal form modificaPassword
$password = mysqli_fetch_assoc(mysqli_query($db,"SELECT volontario.password FROM volontario WHERE volontario.ID = '$idVolontario'"))['password'];

$vecchiaPassword = $_POST['vecchiaPassword'];
$nuovaPassword = $_POST['nuovaPassword'];
$confermaPassword = $_POST['confermaPassword'];

if(password_verify($vecchiaPassword, $password)){
    if($nuovaPassword !== $confermaPassword){
        $_SESSION['errore'] = "passwordDiverse";
        header("location: index.php");
    }elseif(password_verify($nuovaPassword, $password)){
        $_SESSION['errore'] = "passwordUgualeVecchia";
        header("location: index.php");
    }else{
        $nuovaPassword = password_hash($nuovaPassword, PASSWORD_DEFAULT);
    
        $query = "UPDATE volontario SET volontario.password='$nuovaPassword' WHERE volontario.ID = '$idVolontario';";
    
    
        $modificaPassword = mysqli_query($db, $query);
        mysqli_close($db);
        header("location: ./index.php");
    }
}else{
    $_SESSION['errore'] = "passwordVecchia";
    header("location: ./index.php");
}
