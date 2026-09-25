"use strict";

const minute = document.getElementById('minute');
const seconde = document.getElementById('seconde');
const lesLignes = document.getElementById('lesLignes');

const moinsMinute = document.getElementById('moinsMinute');
const plusMinute = document.getElementById('plusMinute');

const moinsSeconde = document.getElementById('moinsSeconde');
const plusSeconde = document.getElementById('plusSeconde');

// -----------------------------------------------------------------------------
// Événements
// -----------------------------------------------------------------------------

minute.addEventListener('input', afficher);
seconde.addEventListener('input', afficher);

moinsMinute.addEventListener('click', () => {

    const valeur = Number(minute.value);
    if (valeur > Number(minute.min)) {
        minute.value = valeur - 1;
        afficher();
    }
});

plusMinute.addEventListener('click', () => {
    const valeur = Number(minute.value);
    if (valeur < Number(minute.max)) {
        minute.value = valeur + 1;
        afficher();
    }
});

moinsSeconde.addEventListener('click', () => {
    const valeur = Number(seconde.value);
    if (valeur > Number(seconde.min)) {
        seconde.value = valeur - 1;
        afficher();
    }
});

plusSeconde.addEventListener('click', () => {
    const valeur = Number(seconde.value);
    if (valeur < Number(seconde.max)) {
        seconde.value = valeur + 1;
        afficher();
    }
});

// -----------------------------------------------------------------------------
// Allures rapides
// -----------------------------------------------------------------------------

document.querySelectorAll('.allure-rapide').forEach(bouton => {
    bouton.addEventListener('click', () => {
        minute.value = bouton.dataset.minute;
        seconde.value = bouton.dataset.seconde;

        document
            .querySelectorAll('.allure-rapide')
            .forEach(element => element.classList.remove('active'));

        bouton.classList.add('active');

        afficher();
    });
});

// -----------------------------------------------------------------------------
// Conversion d'un nombre de secondes en HH:MM:SS
// -----------------------------------------------------------------------------

function getAllure(nbSeconde) {
    const seconde = nbSeconde % 60;
    const resteEnMn = (nbSeconde - seconde) / 60;

    const minute = resteEnMn % 60;
    const heure = (resteEnMn - minute) / 60;

    const h = String(heure).padStart(2, '0');
    const m = String(minute).padStart(2, '0');
    const s = String(seconde).padStart(2, '0');

    return `${h}:${m}:${s}`;

}

// -----------------------------------------------------------------------------
// Affichage du tableau
// -----------------------------------------------------------------------------

function afficher() {
    let nbMinute = Number(minute.value);
    let nbSeconde = Number(seconde.value);

// Valeurs par défaut si la saisie est invalide.
    if (!Number.isFinite(nbMinute)) {
        nbMinute = 4;
    }

    if (!Number.isFinite(nbSeconde)) {
        nbSeconde = 15;
    }

// Limitation des valeurs.
    nbMinute = Math.min(7, Math.max(2, nbMinute));
    nbSeconde = Math.min(59, Math.max(0, nbSeconde));

    minute.value = nbMinute;
    seconde.value = nbSeconde;


// Calcul de l'allure au kilomètre.
    const nbSecondeAuKm = nbMinute * 60 + nbSeconde;


// Distances à afficher.
    const leSemi = [
        1, 2, 3, 4, 5, 6, 7,
        8, 9, 10, 11, 12, 13, 14,
        15, 16, 17, 18, 19, 20, 21.1
    ];

    const leMarathon = [
        22, 23, 24, 25, 26, 27, 28,
        29, 30, 31, 32, 33, 34, 35,
        36, 37, 38, 39, 40, 41, 42.2
    ];

    const le100 = [
        3, 5, 10, 15, 20, 25, 30,
        35, 40, 45, 50, 55, 60, 65,
        70, 75, 80, 85, 90, 95, 100
    ];


// Reconstruction du tableau.
    lesLignes.innerHTML = '';
    for (let i = 0; i < leSemi.length; i++) {
        const tr = lesLignes.insertRow();

        // Semi-marathon.
        tr.insertCell().innerText = `${leSemi[i]} Km`;
        let td = tr.insertCell();

        let nbSeconde = Math.ceil(
            nbSecondeAuKm * leSemi[i]
        );

        td.innerText = getAllure(nbSeconde);


        // Marathon.
        tr.insertCell().innerText = `${leMarathon[i]} Km`;
        td = tr.insertCell();

        nbSeconde = Math.ceil(nbSecondeAuKm * leMarathon[i]);

        td.innerText = getAllure(nbSeconde);


        // 100 km.
        tr.insertCell().innerText = `${le100[i]} Km`;
        td = tr.insertCell();

        nbSeconde = Math.ceil(nbSecondeAuKm * le100[i]);

        td.innerText = getAllure(nbSeconde);
    }

}

// -----------------------------------------------------------------------------
// Initialisation
// -----------------------------------------------------------------------------

afficher();
