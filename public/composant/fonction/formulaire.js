// noinspection JSUnusedGlobalSymbols

'use strict';

// Version 2026.4
// Date version : 06/09/2026

// Import de fonctions utilitaires depuis un module externe

import {afficherSousLeChamp, messageBox} from './afficher.js';

/**
 * Prépare dynamiquement le DOM en insérant une div.messageErreur après chaque champ de saisie.
 * Cette div servira à afficher des messages de validation personnalisés.
 */
export function configurerFormulaire() {
    // Ajout du style une seule fois
    if (!document.getElementById('style-message-erreur')) {
        const style = document.createElement('style');
        style.id = 'style-message-erreur';
        style.textContent = `
            .messageErreur {
                font-size: 0.8rem;
                color: #c00;
                font-style: italic;
                margin : 0;
            }

            @media (max-width: 600px) {
                .messageErreur {
                    margin-left: 0 !important;
                    padding-left: 0 !important;
                }
            }
        `;
        document.head.appendChild(style);
    }

    // Parcours de tous les champs de formulaire, sauf les cases à cocher
    const elements = document.querySelectorAll('input:not([type="checkbox"]), select, textarea');

    for (const element of elements) {
        const parent = element.parentElement;
        let conteneurChamp = null;

        // Vérifie si l'élément est contenu dans une div de classe champ
        if (parent && parent.classList.contains('champ')) {
            conteneurChamp = parent;
        }

        // Détermine si un message d'erreur existe déjà (pour éviter les doublons)
        let messageDejaPresent = false;

        if (conteneurChamp !== null) {
            const elementSuivant = conteneurChamp.nextElementSibling;
            if (elementSuivant && elementSuivant.classList.contains('messageErreur')) {
                messageDejaPresent = true;
            }
        } else {
            const elementSuivant = element.nextElementSibling;
            if (elementSuivant && elementSuivant.classList.contains('messageErreur')) {
                messageDejaPresent = true;
            }
        }

        if (messageDejaPresent) {
            continue;
        }

        // Création du conteneur du message
        const divMessage = document.createElement('div');
        divMessage.classList.add('messageErreur');

        // Insertion du message après le champ ou après le conteneur de classe champ.
        if (conteneurChamp !== null) {
            conteneurChamp.insertAdjacentElement('afterend', divMessage);
        } else {
            element.insertAdjacentElement('afterend', divMessage);
        }
    }
}

/**
 * Configure dynamiquement les contraintes min/max et la valeur d’un champ <input type="date">
 * et met à jour le label associé pour expliciter les règles à l'utilisateur.
 *
 * @param {HTMLInputElement} inputDate - Élément <input type="date"> à configurer (obligatoire).
 * @param {Object} [options] - Options de configuration.
 * @param {string|null} [options.min=null] - Date minimale (format YYYY-MM-DD).
 * @param {string|null} [options.max=null] - Date maximale (format YYYY-MM-DD).
 * @param {string|null} [options.valeur=null] - Valeur initiale à définir.
 */
export function configurerDate(inputDate, {min = null, max = null, valeur = null} = {}) {
    if (!inputDate || !(inputDate instanceof HTMLInputElement)) {
        console.error("Le paramètre fourni n'est pas un champ de date valide.");
        return;
    }

    // Cas limite : ni min ni max → on ne fait rien
    if (!min && !max) {
        console.warn(`Aucune contrainte min/max spécifiée pour ${inputDate.id} — configuration ignorée.`);
        return;
    }

    if (min) {
        inputDate.min = min;
    }
    if (max) {
        inputDate.max = max;
    }
    inputDate.value = valeur || '';

    const formatFr = {day: '2-digit', month: '2-digit', year: 'numeric'};
    const minFr = min ? new Date(min).toLocaleDateString('fr-FR', formatFr) : null;
    const maxFr = max ? new Date(max).toLocaleDateString('fr-FR', formatFr) : null;

    let commentaire = '';
    if (min && max) {
        commentaire = `La date doit être comprise entre le ${minFr} et le ${maxFr}`;
    } else if (min) {
        commentaire = `La date ne peut être antérieure au ${minFr}`;
    } else if (max) {
        commentaire = `La date ne peut excéder le ${maxFr}`;
    }

    const label = document.querySelector(`label[for="${inputDate.id}"]`);
    if (label && commentaire) {
        // Supprimer un éventuel ancien commentaire pour éviter les doublons
        const ancienCommentaire = label.querySelector('.commentaire');
        if (ancienCommentaire) {
            ancienCommentaire.remove();
        }

        // Créer et injecter un nouveau commentaire
        const span = document.createElement('span');
        span.className = 'commentaire';
        span.textContent = ` (${commentaire})`;
        label.appendChild(span);
    }
}


/**
 * Intercepte les frappes clavier dans un champ <input> pour restreindre les caractères autorisés via RegExp.
 * @param {string} idInput attribut id de la balise input
 * @param {RegExp} regExp expression régulière contenant les caractères autorisés
 */
export function filtrerLaSaisie(idInput, regExp) {
    const input = document.getElementById(idInput);
    if (input) {
        input.addEventListener('keydown', (e) => {
            // Autoriser le passage des touches spéciales
            if (e.key.length > 1) {
                return;
            }
            // Vérifier si la touche est un chiffre
            if (!regExp.test(e.key)) {
                e.preventDefault(); // Empêcher la saisie de caractères non contenus dans l'expression régulière
            }
        });
    } else {
        console.error(`L'élément d'entrée ${idInput} n'existe pas.`);
    }
}

/**
 * Valide un champ de formulaire via les API HTML5 de validation.
 * En cas d’échec, bordure rouge + messageBox en modal.
 * @param {object} input balise input
 * @returns {boolean} true si la valeur saisie est valide
 */
export function verifier(input) {
    input.title = input.validationMessage;
    if (input.checkValidity()) {
        input.style.borderColor = '';
        return true;
    } else {
        input.style.borderColor = 'red';
        messageBox(input.validationMessage, 'error');
        return false;
    }
}


/**
 * Contrôle tous les champs input et textarea et select
 * chaque champ xxx doit être suivi d'une balise <div class='messageErreur'></div> pour afficher le message d'erreur : méthode configurerFormulaire
 *  @param {ParentNode} [zone=document] - La zone dans laquelle les champs doivent être contrôlés.
 * @returns {boolean} true si tous les champs respectent les contraintes définies dans leurs attributs pattern, minlength, maxlength, required, min, max
 */
export function donneesValides(zone = document) {
    let valide = true;
    // Sélectionner tous les éléments input et select qui sont required et non désactivés, sauf les cases à cocher
    const lesInputs = zone.querySelectorAll('input[required]:not([disabled]):not([type="checkbox"]), select[required]:not([disabled])');
    // Parcourir et traiter les éléments sélectionnés
    lesInputs.forEach(x => {
        afficherSousLeChamp(x.id);
        if (!x.checkValidity()) {
            valide = false;
        }
    });

    // Vérifier séparément les champs non-required qui ont une valeur
    const champsNonRequired = zone.querySelectorAll('input:not([required]):not([disabled]):not([type="checkbox"]), select:not([required]):not([disabled])');
    champsNonRequired.forEach(x => {
        if (x.value !== '') {
            afficherSousLeChamp(x.id);
            if (!x.checkValidity()) {
                valide = false;
            }
        }
    });
    return valide;
}

/**
 * Contrôle la validité de la date saisie (format jj/mm/aaaa) dans la balise input dont l'id est transmis en paramètre
 * @param {string} idInput attribut id de la balise input
 * @returns {boolean} true si la date est valide
 */
export function dateValide(idInput) {
    const input = document.getElementById(idInput);
    if (input) {
        // Vérifier le format jj/mm/aaaa avec une expression régulière
        const dateRegex = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
        if (!dateRegex.test(input.value)) {
            afficherSousLeChamp(idInput, 'Cette date ne respecte pas le format attendu (jj/mm/aaaa)');
            return false;
        }
        // Récupération des éléments de la date
        const [jour, mois, annee] = input.value.split('/').map(Number);
        // création d'un objet Date avec les éléments de la date
        const date = new Date(annee, mois - 1, jour);
        // La date est valide si l'année, le mois et le jour sont identiques à ceux de l'objet Date
        if (date.getFullYear() === annee && date.getMonth() === mois - 1 && date.getDate() === jour) {
            return true;
        } else {
            afficherSousLeChamp(idInput, 'Cette date n\'est pas valide');
            return false;
        }
    } else {
        console.error(`L'élément d'entrée ${idInput} n'existe pas.`);
    }
}

/**
 * Vide les champs de formulaire (input et textarea) dans une zone spécifiée.
 *
 * @param {ParentNode} [zone=document] - La zone dans laquelle les champs doivent être vidés.
 * Par défaut, il s'agit du document entier.
 */
export function effacerLesChamps(zone = document) {
    for (const input of zone.querySelectorAll('input, textarea')) {
        input.value = '';
    }
}

/**
 * Supprime les messages d'erreur visibles dans une zone, y compris ceux de la zone #msg centrale
 */
export function effacerLesErreurs(zone = document) {
    for (const div of zone.getElementsByClassName('messageErreur')) {
        div.remove();
    }
    const msg = document.getElementById('msg');
    if (msg) {
        msg.innerHTML = '';
    }
}


/**
 * Construit un objet FormData à partir de groupes de valeurs.
 *
 * Les valeurs obligatoires sont toujours transmises.
 * Les valeurs optionnelles sont transmises uniquement si elles
 * ne sont pas nulles, indéfinies ou vides après conversion en texte
 * et suppression des espaces superflus.
 * Les valeurs booléennes sont normalisées en '1' ou '0'.
 *
 * @param {Object} [options={}] - Options de construction du FormData.
 * @param {Object<string, (string|number|boolean|null|undefined)>} [options.obligatoires={}]
 *     Valeurs obligatoires.
 * @param {Object<string, (string|number|boolean|null|undefined)>} [options.optionnels={}]
 *     Valeurs optionnelles.
 * @param {Object<string, boolean>} [options.booleens={}]
 *     Valeurs booléennes à transmettre sous forme de '1' ou '0'.
 * @returns {FormData} Données préparées pour un envoi HTTP.
 */
export function construireFormData({obligatoires = {}, optionnels = {}, booleens = {}} = {}) {
    const formData = new FormData();
    for (const [cle, valeur] of Object.entries(obligatoires)) {
        formData.append(cle, valeur ?? '');
    }
    for (const [cle, valeur] of Object.entries(optionnels)) {
        if (valeur === null || valeur === undefined) {
            continue;
        }
        const texte = String(valeur).trim();
        if (texte !== '') {
            formData.append(cle, texte);
        }
    }
    for (const [cle, valeur] of Object.entries(booleens)) {
        formData.append(cle, valeur ? '1' : '0');
    }
    return formData;
}

/**
 * Permet de valider les données avec la touche "Enter".
 *
 * La validation porte sur l'ensemble du document.
 * La fonction d'action est appelée uniquement si les données sont valides.
 *
 * @param {Function} action - Fonction à exécuter après validation.
 */
export function configurerValidationEntree(action) {
    if (typeof action !== 'function') {
        console.error('L\'action fournie n\'est pas une fonction.');
        return;
    }

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') {
            return;
        }

        event.preventDefault();

        if (donneesValides()) {
            action();
        }
    });
}


/**
 * Configure la navigation au clavier dans un formulaire.
 * La touche "Enter" agit comme la touche "Tab" pour passer au champ suivant.

 */
export function configurerNavigationClavier() {

    const lesElements = [
        ...document.querySelectorAll(
            'input:not([type="hidden"]):not([disabled]), ' +
            'textarea:not([disabled]), ' +
            'select:not([disabled])'
        )
    ];

    lesElements.forEach((element, index) => {

        element.addEventListener('keydown', (event) => {

            if (event.key !== 'PageDown') {
                return;
            }

            event.preventDefault();

            // Passer à l'élément suivant,
            // ou revenir au premier après le dernier.
            const elementSuivant =
                lesElements[(index + 1) % lesElements.length];

            elementSuivant?.focus();
        });
    });
}
