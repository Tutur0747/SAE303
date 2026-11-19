// Images de poisson utilisées par défaut si aucune image personnalisée n'est fournie.
const defaultFishA = createFishImage('#168aad');
const defaultFishB = createFishImage('#f4a261');

// Crée un poisson SVG coloré et le convertit en URL utilisable comme image.
function createFishImage(color) {
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 40">
        <path fill="${color}" d="M12 20C22 5 45 7 54 20 45 33 22 35 12 20ZM12 20 2 9v22l10-11Z"/>
        <circle cx="45" cy="17" r="2" fill="#fff"/>
        <circle cx="45" cy="17" r="1" fill="#17212b"/>
    </svg>`;

    return `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`;
}

function createFishSchool({ // la fonction qui créer un banc de poissons
    proportionA = 0.5, // la proportion de poissons A par rapport au B, par défaut, 0.5
    imageA = defaultFishA, //  L'image utilisé pour les poissons de type A, par défaut, le programme créer un poisson de via la fonction createFishImage
    imageB = defaultFishB, //  L'image utilisé pour les poissons de type B, par défaut, le programme créer un poisson de via la fonction createFishImage
    element = document.getElementById('main')} = {}) { // L'endroit où le banc de poissons sera chargé, par défaut, dans main

    // Test si le paramètre de la proportion contient bien un nombre et si il est fini
    if (typeof proportionA !== 'number' || !Number.isFinite(proportionA) ||
        proportionA < 0 || proportionA > 1) {
        throw new RangeError('proportionA doit être un nombre compris entre 0 et 1.');
    }
    // tesst si element est bien un élément du DOM, le but c'est d'être sûr de pouvoir le placer
    if (!(element instanceof HTMLElement)) {
        throw new Error('Le conteneur du banc de poissons est introuvable.');
    }
    // Vérifie que echarts est bien chargé
    if (!window.echarts) {
        throw new Error('ECharts doit être chargé avant proportion_viz.js.');
    }
    
    const symbolA = toImageSymbol(imageA); // transforme l'image pour les poissons A en symbol
    const symbolB = toImageSymbol(imageB); // transforme l'image pour les poissons B en symbol
    const chart = window.echarts.init(element); // initialisation d'echarts
    const fishCount = 100; // nombre de poissons dans le banc
    const fishACount = Math.round(proportionA * fishCount); // nombre de poissons de type A en se basant sur la proportion
    const positions = makeFishPositions(fishCount); // retourne un tableau avec autant de paire de coordonnées x et y que de poissons dans le banc
    const fishTypes = Array.from({ length: fishCount }, (_, index) => index < fishACount ? 'A' : 'B');

    // Mélange les types pour répartir A et B dans tout le banc plutôt que de les regrouper.
    for (let index = fishTypes.length - 1; index > 0; index--) {
        const swapIndex = Math.floor(Math.random() * (index + 1));
        [fishTypes[index], fishTypes[swapIndex]] = [fishTypes[swapIndex], fishTypes[index]];
    }

    const dataA = [];
    const dataB = [];
    // Associe chaque type de poisson à sa position et sépare les données pour ECharts.
    positions.forEach((position, index) => {
        const point = { value: [position.x, position.y] };
        (fishTypes[index] === 'A' ? dataA : dataB).push(point);
    });

    // Configure l'apparence du banc, ses axes invisibles et l'animation d'apparition.
    chart.setOption({
        // Les poissons apparaissent en moins d'une seconde avec un léger décalage entre eux.
        animationDuration: 900,
        animationDurationUpdate: 0,
        animationEasing: 'cubicOut',
        animationDelay: (index) => (index % 10) * 15,
        // Garde une marge autour des poissons dans la zone de dessin.
        grid: { left: 16, right: 16, top: 16, bottom: 16 },
        // Le repère va de -100 à 100 horizontalement et de -60 à 60 verticalement.
        xAxis: {
            type: 'value',
            min: -100,
            max: 100,
            show: false
        },
        yAxis: {
            type: 'value',
            min: -60,
            max: 60,
            show: false
        },
        tooltip: { show: false },
        // Une série par type permet d'utiliser une image différente pour A et pour B.
        series: [{
            type: 'scatter',
            symbol: symbolA,
            symbolSize: [32, 20],
            data: dataA,
            silent: true
        },
        {
            type: 'scatter',
            symbol: symbolB,
            symbolSize: [32, 20],
            data: dataB,
            silent: true
        }]
    });
    
    return chart;
}

// Prépare une URL d'image au format attendu par ECharts, qu'elle soit locale ou intégrée.
function toImageSymbol(image) {
    if (typeof image !== 'string' || image.trim() === '') {
        throw new TypeError('Chaque image de poisson doit être une URL ou une image data URI.');
    }
    if (image.startsWith('image://')) {
        return image;
    }

    const imageUrl = image.startsWith('data:') ? image : new URL(image, document.baseURI).href;
    return `image://${imageUrl}`;
}

// Place les poissons dans une forme ovale avec des positions aléatoires et espacées.
function makeFishPositions(count) {
    const positions = [];
    // Évite que les images soient trop proches les unes des autres.
    const minDistance = 8;
    let attempts = 0;
    // Limite le nombre d'essais pour éviter une boucle infinie si le placement échoue.
    const maxAttempts = count * 1000;

    // Tire des positions aléatoires jusqu'à placer le nombre de poissons demandé.
    while (positions.length < count && attempts < maxAttempts) {
        attempts++;
        const angle = Math.random() * Math.PI * 2;
        // La racine carrée répartit les poissons dans toute l'ellipse, y compris vers son bord.
        const radius = Math.sqrt(Math.random());
        const candidate = {
            x: Math.cos(angle) * radius * 88,
            y: Math.sin(angle) * radius * 48
        };

        if (positions.every((position) => Math.hypot(position.x - candidate.x, position.y - candidate.y) >= minDistance)) {
            positions.push(candidate);
        }
    }

    // Signale explicitement l'échec plutôt que d'afficher un banc incomplet.
    if (positions.length !== count) {
        throw new Error('Impossible de placer tous les poissons dans le banc.');
    }

    return positions;
}

// Exemple de création : modifier ces paramètres pour personnaliser le banc.
createFishSchool({
    proportionA: 0.5,
    imageA: defaultFishA,
    imageB: defaultFishB
});
