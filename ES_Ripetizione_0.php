<?php

// Inizializzazione variabili
$prodotto = "";
$prezzo = "";
$sconto = "";

$erroreProdotto = "";
$errorePrezzo = "";
$erroreSconto = "";

$esito = "";



// Quando l'utente apre la pagina per la prima volta, la prima parte (questa) viene saltata, pk la richiesta è GET
// quindi si passa subito alla seconda parte (form)
if ($_SERVER["REQUEST_METHOD"] == "POST"){

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
        $colore = "";
    }


    // PREPARAZIONE MSG ERRORE
    $errori = $erroreProdotto . " - " . $errorePrezzo . " - " . $erroreSconto;


    // -----------------
    // FILE CSV
    // -----------------

    // APERTURA FILE CSV
    $file = fopen("log.csv", "a");


    // SCRITTURA INTESTAZIONI
    if (filesize("log.csv") == 0){
        fputcsv($file, array("Data", "Prodotto", "Prezzo", "Sconto", "Esito", "Risparmio", "Prezzo Finale", "Fascia", "Errori"));
    }

    // SCRITTURA DATI
    fputcsv($file, array(date("d/m/Y H:i:s"), $prodotto, $prezzo, $sconto, $esito, $risparmio, $prezzoFinale, $fascia, $errori));


    // CHIUSURA FILE
    fclose($file);

}
?>






<!-- RISULTATO -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calcolatore di sconto</title>
    <link rel="stylesheet" href="stile.css">    <!-- IMPORTANTE!!! -->
</head>
<body>
    <h1>CALCOLATORE DI SCONTO</h1>


    <!-- FORM -->
    <form action="ES_Ripetizione_0.php" method="post">
        <label for="prodotto">Prodotto: </label>
        <input type="text" id="prodotto" name="prodotto" value="<?php echo htmlspecialchars($prodotto); ?>" class="<?php if($erroreProdotto != "") echo "errore"; ?>">   <!-- RICORDA echo!!!! -->
        <!-- Messaggio di Errore -->
        <?php if($erroreProdotto != "") echo "<p class='msg-errore'> $erroreProdotto </p>"; ?>

        <label for="prezzo">Prezzo: </label>
        <input type="text" id="prezzo" name="prezzo" value="<?php echo htmlspecialchars($prezzo); ?>" class="<?php if($errorePrezzo != "") echo "errore"; ?>">
        <?php if($errorePrezzo != "") echo "<p class='msg-errore'> $errorePrezzo </p>"; ?>

        <label for="sconto">Sconto: </label>
        <input type="text" id="sconto" name="sconto" value="<?php echo htmlspecialchars($sconto); ?>" class="<?php if($erroreSconto != "") echo "errore"; ?>">
        <?php if($erroreSconto != "") echo "<p class='msg-errore'> $erroreSconto </p>"; ?>

        <input type="submit" value="CALCOLA">

    </form>


    <!-- RISULTATO -->
    <?php 
    if($esito == "OK"){
    ?>


        <div class="risultato" style="background-color: <?php echo $colore; ?>">
            <h2>RISULTATO</h2>

            <p>Prezzo Finale: <?php echo number_format($prezzoFinale, 2, "," , "."); ?></p>

            <p>Risparmio: <?php echo number_format($risparmio, 2, "," , "."); ?></p>

            <h3><?php echo $fascia; ?></h3>

        </div>
    
    <?php }
    ?>



    <!-- STORICO + TABELLA -->
    <h2>STORICO DEI CALCOLI</h2>

    <?php
    if(file_exists("log.csv") == true){

        $file = fopen("log.csv", "r");

        // Realizzazione Tabella
        echo "<table border='1' style='margin: 20px auto; border-collapse: collapse;'>";

            echo 
            "<tr>
                <th>Prodotto</th>
                <th>Risparmio</th>
                <th>Prezzo Finale</th>
                <th>Fascia</th>
            </tr>";


            // Lettura righe file
            while(($riga = fgetcsv($file)) != false){     // fgetcsv() e fclose() al loro interno contengono sempre $file

                // Mostra i dati(nello storico) se il calcolo è riuscito
                if($riga[0] != "Data" && $riga[4] == "OK"){

                    echo 
                    "<tr>"
                        . "<td>" . htmlspecialchars($riga[1]) . "</td>"
                        . "<td>" . number_format($riga[5], 2, ",", ".") . "</td>"
                        . "<td>" . number_format($riga[6], 2, ",", ".") . "</td>"
                        . "<td>" . htmlspecialchars($riga[7]) . "</td>"
                    . "</tr>";
                }
            }

        echo "</table>";


        // Chiusura del file
        fclose($file);

    }
    else echo "<p> Nessun calcolo effettuato. </p>";
    
    
    ?>

</body>
</html>