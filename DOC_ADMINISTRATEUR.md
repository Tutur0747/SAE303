# Document administrateur

Manec'h CHALMEAU, Eléa CHIRON, Zoélie DENIAU, Precious NWOKOCHA, Arthur ROCHETEAU

TP23E - DWEBDI-3

---

## Sommaire

- [I - Présentation technique](#i---présentation-technique)
- [II - Guide de déploiement](#ii---guide-de-déploiement)
- [III - Configuration possible](#iii---configuration-possible)
- [IV - Conception](#iv---conception)
- [V - Roadmap](#v---roadmap)
- [VI - Bugs tracking](#vi---bugs-tracking)

---

## I - Présentation technique

Atmos est une application web qui aide les étudiant·es de bac +3 à trouver un master en **Sciences de l'univers, de la Terre, de l'espace et de la mer**.

- **Langages :** PHP, HTML, CSS, JavaScript
- **Visualisations :** bibliothèque ECharts (chargée par CDN)
- **Données :** API REST https://la-lab4ce.univ-lemans.fr/masters-stats (données issues de data.gouv.fr)
- **Base de données :** MySQL (prévue, pas encore utilisée)

### Arborescence

```
SAE303/
├── modele/
│   ├── administrer.php
│   └── param.inc.php
└── public/
    ├── index.php
    ├── formation.php
    ├── main.js
    ├── style.css
    ├── img/
    └── visualisation/
        ├── proportion_viz.js
        ├── pression_viz.js
        └── profondeur_liste_viz.js
```

## II - Guide de déploiement

1. Cloner le dépôt :
   ```bash
   git clone https://github.com/Tutur0747/SAE303.git
   ```
2. Placer le dossier dans le serveur web local (WAMP, XAMPP ou Laragon).
3. Créer le fichier `modele/param.inc.php` (il n'est pas sur Git) :
   ```php
   <?php
   define('MYHOST', 'localhost');
   define('MYDB', 'sae303');
   define('MYUSER', 'root');
   define('MYPASS', 'root');
   ```
4. Vérifier que `allow_url_fopen` et l'extension `openssl` sont activés dans `php.ini`.
5. Ouvrir http://localhost/SAE303/public/index.php.

## III - Configuration possible

- **Base de données :** dans `modele/param.inc.php`.
- **URL de l'API :** constante `URL_BASE` dans `modele/administrer.php`.
- **Discipline affichée :** paramètre `sdid=2` dans `getListeFormationsDuSecteurDisciplinaire()`.
- **Année par défaut des statistiques :** `$annee = 2023` dans `public/formation.php`.
- **Banc de poissons :** la fonction `createFishSchool()` de `proportion_viz.js` prend une proportion (entre 0 et 1), deux images de poissons et le conteneur où l'afficher.

## IV - Conception

Le projet suit le modèle **MVC** :

- **Modèle (PHP) :** `administrer.php` récupère les données de l'API.
- **Contrôleur (JS) :** `main.js` transmet les données aux visualisations.
- **Vue (JS) :** un script par visualisation dans `public/visualisation/`.

Les pages PHP affichent directement les données dans le HTML. Les scripts JS lisent ensuite ces données pour créer les visualisations. Le site reste donc consultable même si JavaScript est désactivé.

**Indicateurs prévus :**

| Indicateur | Visualisation |
|---|---|
| Taux de sélectivité, nombre de places, rang du dernier admis | Chiffres clés |
| Proportion femmes / hommes | Banc de poissons |
| Admis depuis la liste d'attente | Bulles imbriquées |
| Insertion professionnelle | À définir |

## V - Roadmap

- Écrire `main.js`, `style.css`, `pression_viz.js` et `profondeur_liste_viz.js`
- Afficher le banc de poissons avec les vraies données sur la fiche formation
- Appliquer la charte graphique
- Créer les pages Recherche, Profil, Paramètres et CGU
- Ajouter la carte des formations et l'insertion professionnelle
- Utiliser la base MySQL pour garder les données en cache

## VI - Bugs tracking

| Fichier | Problème |
|---|---|
| `index.php` | `Administrer.php` est écrit avec une majuscule : la page plante sur un serveur Linux |
| `index.php` | `proportion_viz.js` est chargé mais il n'y a pas d'élément `#main` dans la page (erreur JS) |
| `index.php`, `formation.php` | Si l'API ne répond pas, une erreur PHP s'affiche à l'écran |
| `public/img/` | Dossier vide : l'image de la mascotte et le logo ne s'affichent pas |
| `index.php` | Plusieurs liens mènent vers des pages qui n'existent pas encore |
| `main.js`, `style.css`, `pression_viz.js`, `profondeur_liste_viz.js` | Fichiers encore vides |
