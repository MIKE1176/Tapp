<?php
function firstUpperSentences($string) {
    if (empty($string)) {
        return "";
    }

    // Convertiamo tutto in minuscolo
    $lower = strtolower($string);

    // Definiamo i delimitatori (incluso il backslash con l'escape)
    $delimiters = " -/("; 

    return ucwords($lower, $delimiters);
}

?>