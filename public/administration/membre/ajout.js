"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from "/composant/fonction/ajax.js";
import {configurerFormulaire, donneesValides, filtrerLaSaisie} from "/composant/fonction/formulaire.js";
import {enleverAccent,supprimerEspace} from "/composant/fonction/chaine.js";
import {retournerVers} from "/composant/fonction/afficher.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

// récupération des élements sur l'interface
const msg = document.getElementById('msg');
let nom = document.getElementById('nom');
let prenom = document.getElementById('prenom');
let email = document.getElementById('email');
let btnAjouter = document.getElementById('btnAjouter');


// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

// traitement associé au bouton 'Ajouter'
btnAjouter.onclick = () => {
    // mise en forme des données
    nom.value = enleverAccent(supprimerEspace(nom.value)).toUpperCase();
    prenom.value = enleverAccent(supprimerEspace(prenom.value)).toUpperCase();
    // contrôle des champs de saisie
    if (donneesValides()) {
        ajouter();
    }
};

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

/**
 * Contrôle des informations saisies et demande d'ajout côté serveur
 */
function ajouter() {
    msg.innerText = '';
    appelAjax({
        url : 'ajax/ajouter.php',
        data : {
            columns: {
                nom: nom.value,
                prenom: prenom.value,
                email: email.value
            }
        },
        success : () =>  retournerVers("Le nouveau membre a été ajouté", '.')
    });
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

// limiter les caractères autorisés lors de la frappe sur le champ nom
filtrerLaSaisie('nom', /^[A-Za-z ]$/);
nom.focus();

// limiter les caractères autorisés lors de la frappe sur le champ prenom
filtrerLaSaisie('prenom', /^[A-Za-z ]$/);

configurerFormulaire();
