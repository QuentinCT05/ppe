"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------


// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------


const zoneSelect = document.getElementById("zone");
const laSaison = document.getElementById("laSaison");



// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

// Écouteurs sur les sélections
zoneSelect.addEventListener("change", majCalendrier);
laSaison.addEventListener("change", majCalendrier);

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

function majCalendrier() {
    const zone = zoneSelect.value;
    const saison = laSaison.value;

    fetch(`/calendrier/getcalendrier.php?zone=${zone}&saison=${saison}`)
        .then(r => r.text())
        .then(html => {
            document.getElementById("calendrier").innerHTML = html;
        });
}


// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------



// remplir la zone de liste 'laSaison' avec les années

// Détermination de la saison en cours
const date = new Date();
const annee = date.getFullYear();
const mois = date.getMonth() + 1; // getMonth() va de 0 à 11

const saison = mois > 8 ? annee + 1 : annee;
const saison2 = saison + 1;

laSaison.add(new Option(saison, saison));
laSaison.add(new Option(saison2, saison2));

majCalendrier();