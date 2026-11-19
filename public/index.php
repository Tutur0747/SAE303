<?php 
declare(strict_types=1);
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: text/html; charset=utf-8");
require("./../modele/Administrer.php");
require("./../modele/param.inc.php");
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
    <header>
        <img src="#" alt="Atmos">
        <nav>
            <ul>
                <li><a href="./index.php">Accueil</a></li>
                <li><a href="./recherche.php">Recherche un master</a></li>
                <!-- <li><a href="guide.html">Guide pratique</a></li>         On a vraiment un guide pratique ? -->
                <li><a href="./profil.php">Profil</a></li>
                <li><a href="./parametre.php">Paramètres</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="presentation">
            <h1>ATMOS</h1>
            <a href="./rechercher.php" class="btn">Rechercher un master</a>
            <h2>À la recherche d'un master en Sciences de l'Univers, de la Terre, de l'Espace, la Mer ?</h2>
            <p>Choisir son master ne devrait pas être un saut dans l'inconnu. Nous avons rassemblé et modélisé les données publiques pour éclairer votre orientation. Visualisez en un coup d'œil la répartition des formations sur le territoire, décryptez vos chances d'admission et projetez-vous dans votre future insertion professionnelle.</p>
        </section>
        <section class="mascotte">
            <img src="img/mascotte.webp" alt="Mascotte : un poisson avec un casque d'astonaute">
            <p class="bulle">« Salut ! Je suis Astr'o. Que tu viennes de L3 ou de BUT3, je t'aide à décoder les chiffres des masters publics pour trouver ta prochaine orbite. »</p>
        </section>
        <div>
            <?php

            $liste = [];
            $liste_formations = $administrer->getListeFormationsDuSecteurDisciplinaire();
            foreach ($liste_formations as $formation) {
                $mention = $administrer->getMentionById($formation["mentionId"]);
                echo "<p>" . $mention["nom"] . "</p>";
                echo "<p>" . $formation["parcours"] . "</p>";
                echo '<a href="formation.php?formationId=' . $formation["ifc"] . '"> lien vers la formation </a> <br><br>';
            }


            ?>
        </div>
    </main>
    <footer>
        <h2>ATMOS</h2>
        <ul>
        <li>
            <h3>Réseaux</h3>
            <ul>
            <li></li>
            <li></li>
            <li></li>
            </ul>
        </li>
        <li>
            <h3>Informations légales</h3>
            <ul>
            <li><a href="cgu.html">Conditions générales d'utilisation</a></li>
            </ul>
        </li>
        <li>
            <h3>Naviguation</h3>
            <ul>
            <li><a href="index.html">Accueil</a></li>
            <li><a href="recherche.html">Recherche un master</a></li>
            <li><a href="guide.html">Guide pratique</a></li>
            <li><a href="profil.html"></a>Profil</li>
            <li><a href="parametres.html">Paramètres</a></li>
            </ul>
        </li>
        </ul>
    </footer>
    
</body>
</html>