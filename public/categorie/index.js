"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------
import { getData } from "/composant/fonction/page.js";
import {creerTd, creerTr} from "/composant/fonction/dom.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

/*global html2pdf */

const lesCategories = getData("lesCategories");
const lesLignes = document.getElementById('lesLignes');

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

document.getElementById("btnPdf").addEventListener("click", () => {
    const element = document.getElementById("pdfContent");

    const opt = {
        margin:       0.5,
        filename:     'categories.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save();
});

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

// affichage de la liste des catégories
for (const categorie of lesCategories) {

    // Liste des cellules
    const lesTds = [
        creerTd(categorie.nom),
        creerTd(categorie.id),
        creerTd(categorie.age, { centrer: true }),
        creerTd(categorie.annee, { centrer: true, masquer: true }),
        creerTd(categorie.distance, { masquer: true })
    ];
    // Ajout de la ligne dans le tbody
    lesLignes.appendChild(creerTr(lesTds));
}
