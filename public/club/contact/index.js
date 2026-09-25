"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from "/composant/fonction/ajax.js";
import {genererMessage} from "/composant/fonction/afficher.js";
import {configurerFormulaire, donneesValides, effacerLesChamps, filtrerLaSaisie} from "/composant/fonction/formulaire.js";


// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const nomPrenom = document.getElementById('nomPrenom');
const email = document.getElementById('email');
const message = document.getElementById('message');
const btnEnvoyer = document.getElementById('btnEnvoyer');
const msg = document.getElementById('msg');


// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------
btnEnvoyer.onclick = () => {
    nomPrenom.value = nomPrenom.value.trim();
    if (donneesValides()) {
        envoyer();
    }
};

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

function envoyer() {
    // vider la zone de message msg
    msg.innerHTML = '';
    appelAjax({
        url: 'https://submit-form.com/zairJp8Zv',
        data: {
            nom: nomPrenom.value,
            email: email.value,
            message: message.value
        },
        success: () => {
            effacerLesChamps();
            msg.innerHTML = genererMessage("Votre question est enregistrée, nous vous répondrons dans les plus brefs délais.", 'vert');
        }
    });
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------
// traitements associés au champ nom
filtrerLaSaisie('nomPrenom', /^[A-Za-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ '-]$/);
nomPrenom.focus();
configurerFormulaire();