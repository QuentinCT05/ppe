"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {getData} from "/composant/fonction/page.js";
import {formatDateLong} from "/composant/fonction/date.js";
// -------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const lesEditions = getData("lesEditions");
const lesHoraires = getData("lesHoraires");
const lesLignes = document.getElementById("lesLignes");
const horairesCourses = document.getElementById("horairesCourses");

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

// Affichage des horaires
horairesCourses.innerHTML = lesHoraires;


// Affichage des prochaines éditions
for (const edition of lesEditions) {

    const article = document.createElement("article");
    article.className = "seasons-edition";

    const date = document.createElement("div");
    date.className = "seasons-edition__date";
    date.innerText = formatDateLong(edition.date);

    const description = document.createElement("div");
    description.className = "seasons-edition__description";
    description.innerHTML = edition.description;

    article.appendChild(date);
    article.appendChild(description);

    lesLignes.appendChild(article);
}
