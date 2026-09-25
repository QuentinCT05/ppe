"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {getData} from "/composant/fonction/page.js";
import {appelAjax} from "/composant/fonction/ajax.js";
import {afficherToast} from "/composant/fonction/afficher.js";
import {configurerFormulaire, donneesValides, filtrerLaSaisie} from "/composant/fonction/formulaire.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const data = getData("data");
const nom = document.getElementById('nom');
const email = document.getElementById('email');
const telephone = document.getElementById('telephone');
const autMail = document.getElementById('autMail');
const btnModifier = document.getElementById('btnModifier');
const msg = document.getElementById('msg');

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------


// -----------------------------------------------------------------------------------
// procédures évènementielles
// -----------------------------------------------------------------------------------

btnModifier.onclick = () => {
    if (donneesValides()) {
        modifier();
    }
};

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

function modifier() {
    msg.innerHTML = '';
    appelAjax({
        url: 'ajax/modifier.php',
        data: {
            primaryKey: data.id,
            columns: {
                telephone: telephone.value === '' ? null : telephone.value,
                autMail: autMail.checked ? '1' : '0'
            }
        },
        success: () => afficherToast("Opération réalisée avec succès")
    });
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------
// Traitement sur le champ téléphone
filtrerLaSaisie('telephone', /[0-9]/);
configurerFormulaire();

// afficher les informations
// afficher les informations
nom.textContent = data.nom + ' ' + data.prenom;
email.textContent = data.email;

// le téléphone n'est pas forcément renseigné
if (data.telephone !== null) {
    telephone.value = data.telephone;
} else {
    telephone.value = '';
}

// Case pour l'autorisation de l'affichage du mail dans l'annuaire du club
autMail.checked = data.autMail === 1;

