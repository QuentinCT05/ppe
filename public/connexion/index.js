"use strict";
// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------
import {configurerFormulaire, donneesValides} from "/composant/fonction/formulaire.js";
import {appelAjax } from "/composant/fonction/ajax.js";
import {initPasswordToggles} from "/composant/fonction/password.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const login = document.getElementById('login');
const password = document.getElementById('password');
const btnValider = document.getElementById('btnValider');
const msg = document.getElementById('msg');

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

// la touche entrée permet de valider le formulaire
document.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
        if (donneesValides()) {
            connecter();
        }
    }
});

btnValider.onclick = () => {
    if (donneesValides()) {
        connecter();
    }
};

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

/**
 * Vérifie que les champs de saisi sont bien renseignés
 * en absence d'erreur la demande de connexion est envoyée
 * En cas de succès l'utilisateur est redirigé vers la page monprofil.php
 */
function connecter() {
    // Vider la zone de message utilisateur
    msg.innerHTML = "";

    appelAjax({
        url: 'ajax/connecter.php',
        data: {
            login: login.value,
            password: password.value,
        },
        success: (data) => {
            // Redirection vers la page renvoyée par le serveur
            location.href = data.success;
        }
    });
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

login.focus();

initPasswordToggles();
configurerFormulaire();
