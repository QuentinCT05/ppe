"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------
import {appelAjax} from "/composant/fonction/ajax.js";
import {confirmer, genererMessage} from "/composant/fonction/afficher.js";
import {getData} from "/composant/fonction/page.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

/* global autoComplete */

const lesMembres = getData("lesMembres");
const lesAdministrateurs = getData("lesAdministrateurs");


let btnAjouter = document.getElementById('btnAjouter');
let msg = document.getElementById('msg');
let search = document.getElementById('search');
let lesLignes = document.getElementById('lesLignes');

let id = null; // identifiant du membre sélectionné

// -----------------------------------------------------------------------------------
// procédures évènementielles
// -----------------------------------------------------------------------------------

btnAjouter.onclick = () => {
    msg.innerHTML = "";
    if (id == null) {
        msg.innerHTML = genererMessage("Il faut sélectionner un membre dans la liste à partir de la saisie de son nom", "orange");
    } else {
        ajouter(id);
    }
};

search.onfocus = () => {
    msg.innerHTML = "";
    search.value = "";
};


// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------
/**
 * Affiche la liste des administrateurs
 */
function afficherLesAdministrateurs() {
    if (lesAdministrateurs.length === 0) {
        lesLignes.innerHTML = "<p>Aucune membre n'est actuellement administrateur</p>";
    } else {
        lesLignes.innerHTML = "";
        for (const administrateur of lesAdministrateurs) {
            const tr = lesLignes.insertRow();
            tr.id = administrateur.id;

            const td = tr.insertCell();
            const btnSupprimer = document.createElement('span');
            btnSupprimer.textContent = '✘';
            btnSupprimer.style.color = 'red';
            btnSupprimer.style.cursor = 'pointer';
            btnSupprimer.title = 'Supprimer';
            btnSupprimer.onclick = () => confirmer(() => supprimer(administrateur.id));
            td.appendChild(btnSupprimer);

            // colonne nom
            tr.insertCell().innerText = administrateur.nom;
        }
    }
}

/**
 * Ajoute un administrateur
 */
function ajouter() {
    // appel ajax pour ajouter un administrateur
    appelAjax({
        url: 'ajax/ajouter.php',
        data: { id : id},
        success: () => {
            // mise à jour des tableaux lesMembres et lesAdministrateurs et rafraîchissement de l'affichage
            transfererMembre(lesMembres, lesAdministrateurs, id);
            afficherLesAdministrateurs();
        },
    });
}

/**
 * suppression d'un administrateur
 * @param {number} id - Identifiant du membre à supprimer
 */
function supprimer(id) {
    // Vider la zone de message utilisateur
    msg.innerHTML = "";
    appelAjax({
        url: 'ajax/supprimer.php',
        data: {
            id: id,
            table: 'administrateur'
        },
        success: () => {
            // mise à jour des tableaux lesMembres et lesAdministrateurs et rafraîchissement de l'affichage
            transfererMembre(lesAdministrateurs, lesMembres, id);
            afficherLesAdministrateurs();
        },
    });
}

/**
 * Transfère un membre d’un tableau source vers un tableau destination.
 * Trie le tableau destination après ajout.
 *
 * @param {Array} sourceTableau - Tableau de départ (ex : lesMembres ou lesAdministrateurs)
 * @param {Array} destinationTableau - Tableau d'arrivée
 * @param {number} id - Identifiant du membre à transférer
 */
function transfererMembre(sourceTableau, destinationTableau, id) {
    // Récupération de l'index de l'élément dans le tableau source
    const index = sourceTableau.findIndex(item => item.id === id);
    // Transfert de l'élément du tableau source vers le tableau destination
    const membre = sourceTableau[index];
    // ajout de l'élément au tableau destination
    destinationTableau.push({ nom: membre.nom, id: membre.id });
    // tri du tableau destination
    destinationTableau.sort((a, b) => a.nom.localeCompare(b.nom));
    // suppression de l'élément du tableau source
    sourceTableau.splice(index, 1);
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------


// mise en place de l'autocomplétion sur le champ de recherche : le nom
//noinspection JSPotentiallyInvalidConstructorUsage
new autoComplete({
    data: {
        src: lesMembres,
        keys: ["nom"]
    },
    placeHolder: "Nom du membre",
    selector: "#search",
    threshold: 1,
    debounce: 300,
    searchEngine: "strict",
    resultsList: {
        maxResults: 10
    },
    resultItem: {
        highlight: true
    },
    events: {
        input: {
            selection: (event) => {
                const selection = event.detail.selection.value;
                id = selection.id;
                search.value = selection.nom;
                search.classList.remove("erreur");
                btnAjouter.focus();
            },
            open: () => {
                id = null;
            },
            results: (event) => {
                search.classList.remove("erreur");
                const results = event.detail.results;
                const nb = results.length;
                if (nb === 0) {
                    search.classList.add("erreur");
                }
            },
        }
    }
});

// affichage initial
afficherLesAdministrateurs();