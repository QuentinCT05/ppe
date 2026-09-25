"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {getData} from "/composant/fonction/data.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const contenu = document.getElementById('contenu');
const lesFonctions = getData('lesFonctions');


// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------
for (const element of lesFonctions) {

    const a = document.createElement("a");
    a.className = "admin-card";
    a.href = element.href;

    // Contenu
    const cardContent = document.createElement("span");
    cardContent.className = "admin-card__content";

    const title = document.createElement("span");
    title.className = "admin-card__title";
    title.textContent = element.label;

    cardContent.appendChild(title);

    a.appendChild(cardContent);


    contenu.appendChild(a);
}

