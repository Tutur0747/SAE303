<?php
declare(strict_types=1);
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: text/html; charset=utf-8");
require("./modele/Administrer.php");
require("./modele/param.inc.php");
$administrer = new Administrer(MYHOST, MYDB, MYUSER, MYPASS);

if (!isset($_GET["formationId"])) {
    // header("location=index.php");
}

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
    
</body>
</html>