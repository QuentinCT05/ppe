"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {getData} from "/composant/fonction/page.js";
import {appelAjax} from "/composant/fonction/ajax.js";
import {configurerFormulaire, donneesValides, effacerLesChamps} from "/composant/fonction/formulaire.js";
import {afficherSousLeChamp, afficherToast, confirmer} from "/composant/fonction/afficher.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const lesFonctions = getData("lesFonctions");

const nom = document.getElementById('nom');
const repertoire = document.getElementById('repertoire');
const msg = document.getElementById('msg');
const btnAjouter = document.getElementById('btnAjouter');
const lesLignes = document.getElementById('lesLignes');

// -----------------------------------------------------------------------------------
// procédures évènementielles
// -----------------------------------------------------------------------------------

btnAjouter.onclick = () => {
    msg.innerHTML = "";

    if (donneesValides() && controlerFonction()) {
        ajouter();
    }
};

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

// fonction d'affichage
function afficherLesFonctions() {
    if (lesFonctions.length === 0) {
        lesLignes.innerHTML = "<p>Aucune donnée à afficher</p>";
    } else {
        effacerLesChamps();
        lesLignes.innerHTML = "";

        for (const fonction of lesFonctions) {
            const tr = lesLignes.insertRow();
            tr.insertCell().innerText = fonction.repertoire;
            tr.insertCell().innerText = fonction.nom;
        }
    }
}

/**
 * Contrôle qu'une fonction n'existe pas déjà.
 *
 * Le répertoire et le nom doivent être uniques.
 *
 * @returns {boolean} true si la fonction peut être ajoutée
 */
function controlerFonction() {
    const valeurRepertoire = repertoire.value.trim().toLowerCase();
    const valeurNom = nom.value.trim().toLowerCase();

    // Recherche d'un répertoire déjà utilisé
    const repertoireExiste = lesFonctions.some(
        fonction => fonction.repertoire.trim().toLowerCase() === valeurRepertoire
    );

    if (repertoireExiste) {
        afficherSousLeChamp(repertoire, "Ce répertoire est déjà utilisé.");
        repertoire.focus();
        return false;
    }

    // Recherche d'un nom déjà utilisé
    const nomExiste = lesFonctions.some(
        fonction => fonction.nom.trim().toLowerCase() === valeurNom
    );

    if (nomExiste) {
        afficherSousLeChamp(nom, "Ce nom de fonction est déjà utilisé.");
        nom.focus();
        return false;
    }

    return true;
}

/**
 * Ajout d'une fonction
 */
function ajouter() {
    // vider la zone de message msg
    msg.innerHTML = "";
    appelAjax({
        url: 'ajax/ajouter.php',
        data: {
            columns: {
                repertoire: repertoire.value,
                nom: nom.value
            }
        },
        success: () => {
            afficherToast("Ajout réussi !");
            // mettre à jour le tableau lesFonctions
            lesFonctions.push({repertoire: repertoire.value, nom: nom.value});
            // trier le tableau
            lesFonctions.sort((a, b) => a.nom.localeCompare(b.nom));
            // vider les champs
            effacerLesChamps();
            // mettre à jour l'affichage
            repertoire.focus();
            afficherLesFonctions();
        }
    });
}



// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------
// configuration du formulaire
configurerFormulaire();
// affichage initial
afficherLesFonctions();

