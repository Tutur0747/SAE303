<?php 
declare(strict_types=1);
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: text/html; charset=utf-8");
require("./modele/Administrer.php");
require("./modele/param.inc.php");
$administrer = new Administrer(MYHOST, MYDB, MYUSER, MYPASS);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atmos</title>
    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
    <script src="./visualisation/proportion_viz.js" defer></script>
</head>
<body>
    <h1>Banc de poissons</h1>
    <div id="main" style="border: 1px solid black; height: min(70vh, 560px); min-height: 360px; width: 40%;">

    </div>
    <div>
        <?php

        $liste = [];
        $liste_formations = $administrer->getListeFormationsDuSecteurDisciplinaire();
        foreach ($liste_formations as $formation) {
            $mention = $administrer->getMentionById($formation["mentionId"]);
            echo "<p>" . $mention["nom"] . "</p>";
            echo "<p>" . $formation["parcours"] . "</p> <br>";
        }


        ?>
    </div>
</body>
</html>