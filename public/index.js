"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {getData} from "/composant/fonction/page.js";
import {initialiserToutesLesCartes} from "/composant/fonction/openclose.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

// récupération des données transmises à l'interface
const laProchaineEpreuve = getData("laProchaineEpreuve");


// Récupération des éléments de l'interface

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------


// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

// affichage de la prochaine épreuve
// Convertit la chaîne ou le timestamp en objet Date
const dateObj = new Date(laProchaineEpreuve.date);

// Option 1 : Format long complet avec le jour de la semaine (ex: "dimanche 12 octobre 2026")
const dateFormatee = dateObj.toLocaleDateString('fr-FR', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric'
});

// Injection dans l'élément HTML
document.getElementById("dateEpreuve").innerText = dateFormatee;



initialiserToutesLesCartes();








