"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------
import {activerTri } from "/composant/fonction/tableau.js";
import { getData } from "/composant/fonction/page.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const data = getData('lesMembres');
const lesLignes = document.getElementById('lesLignes');

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

function afficher() {
    lesLignes.innerHTML = ''; // Effacer les lignes existantes
    for (const membre of data) {
        let tr = lesLignes.insertRow();
        let td = tr.insertCell();
        td.innerText = membre.login;
        td.classList.add('masquer');
        tr.insertCell().innerText = membre.nom + ' ' + membre.prenom;
        tr.insertCell().innerText = membre.email;
        td = tr.insertCell();
        td.innerText = membre.afficherMail;
        td.classList.add('masquer');
        td = tr.insertCell();
        td.innerText = membre.photo;
        td.classList.add('masquer');
        td = tr.insertCell();
        td.innerText = membre.telephone;
        td.classList.add('masquer');
    }
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------
activerTri({
    idTable: "leTableau",
    getData: () => data,
    afficher: afficher,
    triInitial: {
        colonne: "nomPrenom",
        sens: "asc"
    }
});

afficher();