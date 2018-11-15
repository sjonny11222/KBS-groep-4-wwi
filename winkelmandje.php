<?php

// Winkelmandje: GEMAAKT DOOR DOUWE
// Start sessie
session_start();


// Inlog gegevens
$db = "mysql:host=localhost;dbname=wideworldimporters;port=3306";
$user = "root";
$pass = "";

$notification;

// Verwijder artikel
if (filter_has_var(INPUT_GET, 'verwijder')) {
    if (verwijder(filter_input(INPUT_GET, 'verwijder', FILTER_SANITIZE_NUMBER_INT))) {
        // Het artikel is verwijderd.
        $notification = array('content' => "Artikel verwijderd", 'color' => 'green', 'target' => 'winkelmandje');
    }
}


// Toenemen aantal artikelen
if (filter_has_var(INPUT_GET, 'toenemen')) {
    if (toenemen(filter_input(INPUT_GET, 'toenemen', FILTER_SANITIZE_NUMBER_INT))) {
        // De voorraad is toegenomen.
        $notification = array('content' => "Artikel toegevoegd", 'color' => 'green', 'target' => 'artikelpagina');
    } else {
        // De voorraad is niet toegenomen.
        $notification = array('content' => "Meer voorraad is er niet.", 'color' => 'red');
    }
}


// Afnemen aantal artikelen
if (filter_has_var(INPUT_GET, 'afnemen')) {
    if (!afnemen(filter_input(INPUT_GET, 'afnemen', FILTER_SANITIZE_NUMBER_INT))) {
        // Artikel verwijderd
        $notification = array('content' => "Artikel verwijderd", 'color' => 'green', 'target' => 'winkelmandje');
    }
}


// Als er een melding is, deze in de session zetten.
if (isset($notification)) {
    $_SESSION['notification'] = $notification;
}


// Gebruiker terug sturen naar origin. Als die niet beschikbaar is: homepage.
if (filter_has_var(INPUT_GET, 'origin')) {
    header('location: ' . filter_input(INPUT_GET, 'origin', FILTER_SANITIZE_STRING));
} else {
    header('location: ./winkelmandje'); // MOET HOMEPAGE WORDEN!
}

// Verwijder artikel uit de array.
function verwijder($artikel) {
    unset($_SESSION['winkelmandje'][$artikel]);
    return 1;
}

// Hoeveelheid toenemen met 1: 1 als het goed gaat. 0 bij error.
function toenemen($artikel) {
    // BRON: http://php.net/language.variables.scope
    global $db, $user, $pass;

    // Verbinding maken
    // BRON: PowerPoint Workshop databaseconnectie ELO
    $pdo = new PDO($db, $user, $pass);

    // Voorbereiden
    $stmt = $pdo->prepare("SELECT * FROM stockitemholdings WHERE StockItemID=?");

    // Uitvoeren
    $stmt->execute(array($artikel));

    // Als er resultaten uit de database zijn...
    if ($stmt->rowCount() > 0) {
        // Resultaten in een array zetten.
        $row = $stmt->fetch();

        if ($row['QuantityOnHand'] > $_SESSION['winkelmandje'][$artikel]) {
            // De voorraad is groter dan het aantal van dit artikel in het winkelmandje.
            // Er wordt een extra product toegevoegd aan het winkelmandje
            $_SESSION['winkelmandje'][$artikel] = $_SESSION['winkelmandje'][$artikel] + 1;

            // Verbinding verbreken
            $pdo = NULL;
            return 1;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

// Hoeveeheid afnemen met 1: 1 als het goed gaat. 0 bij verwijderen.
// Als het artikel in het winkelmandje aantal 1 of lager heeft, wordt hij verwijderd.
function afnemen($artikel) {
    if ($_SESSION['winkelmandje'][$artikel] <= 1) {
        verwijder($artikel);
        return 0;
    } else {
        $_SESSION['winkelmandje'][$artikel] = $_SESSION['winkelmandje'][$artikel] - 1;
        return 1;
    }
}

?>
