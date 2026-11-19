<?php
declare(strict_types=1);
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: text/html; charset=utf-8");
require_once __DIR__ . "/../modele/administrer.php";
require_once __DIR__ . "/../modele/param.inc.php";
$administrer = new Administrer(MYHOST, MYDB, MYUSER, MYPASS);

if (!isset($_GET['formationId']) || !is_string($_GET['formationId']) ||
    !preg_match('/^[A-Za-z0-9]+$/D', $_GET['formationId'])) {
    http_response_code(400);
    exit('Identifiant de formation invalide.');
}

$formationIfc = $_GET['formationId'];
$annee = 2023;
if (array_key_exists('annee', $_GET)) {
    $anneeDemandee = is_string($_GET['annee'])
        ? filter_var($_GET['annee'], FILTER_VALIDATE_INT)
        : false;
    if ($anneeDemandee === false || $anneeDemandee < 2000 || $anneeDemandee > (int) date('Y')) {
        http_response_code(400);
        exit('Année invalide.');
    }
    $annee = $anneeDemandee;
}

$formation = $administrer->getFormationById($formationIfc);
$nbCandidats = $administrer->getNbCan($formationIfc, $annee);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atmos</title>
</head>
<body>
    <h1>Formation</h1>
    <p>mention : <?= htmlspecialchars($formation['mention'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
    <p>parcours : <?= htmlspecialchars($formation['parcours'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>

    <form method="get">
        <input type="hidden" name="formationId" value="<?= htmlspecialchars($formationIfc, ENT_QUOTES, 'UTF-8') ?>">
        <label for="annee">Année de collecte :</label>
        <input type="number" id="annee" name="annee" min="2000" max="<?= (int) date('Y') ?>" value="<?= $annee ?>" required>
        <button type="submit">Afficher</button>
    </form>

    <?php if ($nbCandidats === null): ?>
        <p>Aucune statistique de candidature disponible pour <?= $annee ?>.</p>
    <?php else: ?>
        <p>Nombre de candidat·es en <?= $annee ?> : <?= $nbCandidats ?></p>
    <?php endif; ?>

    <!-- <div id="main" style="border: 1px solid black; height: min(70vh, 560px); min-height: 360px; width: 40%;"></div> -->

</body>
</html>