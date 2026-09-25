"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------
import { ucWord } from "/composant/fonction/chaine.js";

import {getData} from "/composant/fonction/page.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------
const divInformation = document.getElementById("divInformation");
const divDocumentation = document.getElementById("divDocumentation");
const divFonction = document.getElementById("divFonction");
const mesFonctions = document.getElementById("mesFonctions");

const lesFonctions = getData("lesFonctions");



// -----------------------------------------------------------------------------------
// procédures évènementielles
// -----------------------------------------------------------------------------------

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------


// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

if (lesFonctions && lesFonctions.length > 0) {
    mesFonctions.style.display = "block";

    // Création du conteneur <ul> pour les items
    const ul = document.createElement("ul");
    ul.className = "admin-fonctions-list";

    for (const element of lesFonctions) {
        const li = document.createElement("li");
        li.className = "admin-fonctions-item";

        const a = document.createElement("a");
        a.className = "admin-fonctions-link";
        a.href = "/administration/" + element.repertoire;
        a.textContent = element.nom;

        li.appendChild(a);
        ul.appendChild(li);
    }

    divFonction.appendChild(ul);
} else {
    mesFonctions.style.display = "none";
}



