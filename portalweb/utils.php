<?php

function firstUpperSenteces($arrayKey){
    $a = "";
    for ($i=0; $i < count(explode(" ", $_POST[$arrayKey])); $i++) { 
        if(count(explode(" ", $_POST[$arrayKey]))==1){
            $a = ucfirst(strtolower($_POST[$arrayKey]));
        }else{
            $a = $a . ucfirst(strtolower(explode(" ", $_POST[$arrayKey])[$i]));
            if(($i+1)!=count(explode(" ", $_POST[$arrayKey]))){                 //aggiungo +1 perchè quando arrivo all'ultima parola, lo spazio non me lo deve mettere
                $a = $a . " ";
            }
        }
    }
    return $a;
}