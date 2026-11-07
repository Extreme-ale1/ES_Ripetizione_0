<?php

// Inizializzazione variabili
$prodotto = "";
$prezzo = "";
$sconto = "";

$erroreProdotto = "";
$errorePrezzo = "";
$erroreSconto = "";

$esito = "";


if ($_SERVER["REQUEST_METHOD" == POST]){

    // RECUPERO DEI DATI
    $prodotto = trim($_POST["prodotto"]);
    $prezzo = trim($_POST["prezzo"]);
    $sconto = trim($_POST["sconto"]);


    // CONTROLLO SINGOLI DATI

    // Prodotto
    if ($prodotto == "") {
        $erroreProdotto = "Prodotto mancante";
    }
    elseif (strlen($prodotto) > 30) {
        $erroreProdotto = "Lunghezza prodotto > 30";
    }

    // Prezzo
    if ($prezzo == "") {
        $errorePrezzo = "Prezzo mancante";
    }
    elseif (!is_numeric($prezzo)) {
        $errorePrezzo = "Prezzo non numerico";
    }
    elseif ($prezzo <= 0) {
        $errorePrezzo = "Prezzo <= 0";
    }

    // Sconto
    if ($sconto == "") {
        $erroreSconto = "Sconto mancante";
    }
    elseif (!is_numeric($sconto)) {
        $erroreSconto = "Sconto non numerico";
    }
    elseif ($sconto != intval($sconto)) {
        $erroreSconto = "Sconto non intero";
    }
    elseif ($sconto < 1 || $sconto > 90) {
        $erroreSconto = "Sconto fuori limite";
    }


    // CONTROLLO DEI 3 DATI
    if($errorePrezzo == "" && $erroreProdotto == "" && $erroreSconto ==""){

        $esito = "OK";

        // CALCOLO PREZZO FINALE E RISPARMIO
        $risparmio = $prezzo * $sconto / 100;
        $prezzoFinale = $prezzo - $risparmio;


        // ASSEGNAZIONE DI FASCIA E COLORE
        $fascia = "";
        $colore = "";

        if ($sconto < 20) {
            $fascia = "PICCOLO";
            $colore = "#e20bf5";
        }
        elseif ($sconto < 50) {
            $fascia = "BUONO";
            $colore = "#15ff00";
        }
        else {
            $fascia = "SUPER";
            $colore = "#0051ff";
        }
    }
    else{
        $esito = "KO";

        // Campi da lasciare vuoti nel CSV
        $risparmio = "";
        $prezzoFinale = "";
        $fascia = "";
    }


    // PREPARAZIONE MSG ERRORE
    $errori = $erroreProdotto . " - " . $errorePrezzo . " - " . $erroreSconto;


    // -----------------
    // FILE CSV
    // -----------------

    // APERTURA FILE CSV
    $file = fopen("log.csv", "a");


    // SCRITTURA INTESTAZIONI
    if (filesize($file) == 0){
        fputcsv($file, array("Data", "Prodotto", "Prezzo", "Sconto", "Esito", "Risparmio", "Prezzo Finale", "Fascia", "Errori"))
    }

    // SCRITTURA DATI
    fputcsv($file, array(date("d/m/Y H:i:s"), $prodotto, $prezzo, $sconto, $esito, $risparmio, $prezzoFinale, $fascia, $errori))


    // CHIUSURA FILE
    fclose($file);

}
?>




































?>