"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from "/composant/fonction/ajax.js";
import {configurerFormulaire, donneesValides } from "/composant/fonction/formulaire.js";
import {afficherToast} from "/composant/fonction/afficher.js";
import {getData} from "/composant/fonction/data.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

/* global tinymce */

const lesEpreuves = getData('lesEpreuves');

// récupération des éléments de l'interface
const saison = document.getElementById('saison');
const msg = document.getElementById('msg');
const date = document.getElementById('date');
const description = document.getElementById('description');
const btnModifier = document.getElementById('btnModifier');

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

// demande de modification
btnModifier.onclick = () => {
    if (donneesValides()) {
            modifier();
    }
};

// sur le changement de l'épreuve (saison), il faut afficher les informations de cette épreuve
saison.onchange = afficher;

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

/**
 * Affiche les informations de l'épreuve sélectionnée
 */
function afficher() {
    // Récupération de l'épreuve
    const epreuve = lesEpreuves[saison.selectedIndex];
    // affichage des informations sur l'interface
    date.value = epreuve.date;
    description.value = epreuve.description;
    // mise à jour de l'éditeur TinyMCE
    tinymce.get('description').setContent(epreuve.description);
}

function modifier() {
    msg.innerHTML = '';
    // transmission des paramètres
    const columns = {};
    columns.date = date.value;
    columns.description = description.value;

    appelAjax({
        url: 'ajax/modifier.php',
        data: {
            primaryKey: saison.value,
            columns: columns
        },
        success: data => afficherToast(data.message)
    });
}


// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

// Initialisation de TinyMCE
tinymce.init({
    license_key: 'gpl',
    selector: '#description',
    menubar: false,
    plugins: 'link lists table autoresize code',
    toolbar: [
        'undo redo | styles | bold italic underline | forecolor backcolor | fontsizeselect | link | bullist numlist outdent indent | table | code'
    ],
    fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt',
    setup: function (editor) {
        editor.on('change', function () {
            description.value = editor.getContent();
        });
    }
});


// mise en place des balises div de class 'messageErreur' sur chaque champ de saisie
configurerFormulaire();


// alimentation de la zone de liste des épreuves
for (const epreuve of lesEpreuves) {
    saison.add(new Option(epreuve.saison, epreuve.saison));
}

// charger les informations de l'épreuve actuellement sélectionnée
afficher();





