// noinspection JSUnusedGlobalSymbols

"use strict";

// Version 2026.2
// Date : 11/08/2026
// Gestion de l'affichage des cartes avec mémorisation de l'état dans le localStorage
// Module auto-suffisant : injecte ses styles et expose des fonctions d'API
// Dernière modification
//    Arrondir le header seul quand la carte est fermée

/**
 * Clé utilisée dans le localStorage pour mémoriser l'état d'ouverture des cartes.
 */

const CLE_STORAGE = 'etatCartes';
let etats = JSON.parse(localStorage.getItem(CLE_STORAGE)) || {};

/**
 * Sauvegarde les états dans le localStorage
 */
function sauvegarderEtats() {
    localStorage.setItem(CLE_STORAGE, JSON.stringify(etats));
}

/**
 * Affiche ou masque un contenu, met à jour l'icône associée et sauvegarde l'état
 * @param {HTMLElement} contenu - Élément à afficher/masquer
 * @param {HTMLElement} icone - Élément icône à modifier
 * @param {string} id - Identifiant de la carte
 * @param {boolean} [forcerEtat] - true pour afficher, false pour masquer, sinon toggle
 */
export function basculerAffichage(contenu, icone, id, forcerEtat) {
    const visible = window.getComputedStyle(contenu).display !== 'none';
    const afficher = (forcerEtat !== undefined) ? forcerEtat : !visible;

    // Afficher ou masquer le contenu
    contenu.style.display = afficher ? '' : 'none';
    // Mettre à jour l’icône
    icone.classList.toggle('iconeOuvrir', !afficher);
    icone.classList.toggle('iconeFermer', afficher);

    // Ajouter / retirer la classe d'arrondi sur le header
    const entete = icone.parentElement;
    if (!afficher) {
        entete.classList.add("arrondi-seul");
    } else {
        entete.classList.remove("arrondi-seul");
    }

    // Sauvegarder l'état
    etats[id] = afficher;
    sauvegarderEtats();
}

/**
 * Récupère les éléments d'une carte : en-tête, contenu, icône, identifiant.
 *
 * @param {HTMLElement} carte
 * @param {number|null} [index=null]
 * @returns {{entete: HTMLElement, contenu: HTMLElement, icone: HTMLElement|null, id: string}|null}
 */
export function obtenirElementsCarte(carte, index = null) {
    if (!carte) {
        return null;
    }
    // On cherche l'en-tête
    const enteteElement = carte.querySelector('.card-header');
    if (!(enteteElement instanceof HTMLElement)) {
        return null;
    }
    // On cherche le premier enfant non-header qui représente le corps
    const contenu = [...carte.children].find(child =>
        child !== enteteElement && !child.classList.contains('card-header')
    );

    if (!(contenu instanceof HTMLElement)) {
        return null;
    }
    const id = carte.id || (index !== null ? `carte-${index}` : null);
    if (!id) {
        console.warn("Carte sans identifiant ni index fourni :", carte);
        return null;
    }
    const iconeElement = enteteElement.querySelector('.iconeOuvrir, .iconeFermer');
    const icone = iconeElement instanceof HTMLElement ? iconeElement : null;
    return {entete: enteteElement, contenu, icone, id};
}

/**
 * Initialisation d'une carte
 * @param {HTMLElement} carte
 * @param {number|null} index
 */
export function initialiserCarte(carte, index = null) {
    const elements = obtenirElementsCarte(carte, index);
    if (!elements) {
        return;
    }
    const {entete, contenu, icone, id} = elements;
    // Déterminer l'état initial (stocké ou par défaut visible)
    const visible = etats[id] !== undefined ? etats[id] : window.getComputedStyle(contenu).display !== 'none';
    contenu.style.display = visible ? '' : 'none';
    let nouvelleIcone = icone;
    if (!icone) {
        nouvelleIcone = document.createElement('i');
        nouvelleIcone.classList.add(visible ? 'iconeFermer' : 'iconeOuvrir', 'float-end');
        entete.appendChild(nouvelleIcone);
    } else {
        nouvelleIcone.classList.toggle('iconeOuvrir', !visible);
        nouvelleIcone.classList.toggle('iconeFermer', visible);
    }

    // Gestion de l'arrondi si la carte est fermée
    if (!visible) {
        entete.classList.add('arrondi-seul');
    } else {
        entete.classList.remove('arrondi-seul');
    }

    entete.style.cursor = 'pointer';
    entete.onclick = () => basculerAffichage(contenu, nouvelleIcone, id);
}

/**
 * Initialise toutes les cartes présentes dans le document
 */
export function initialiserToutesLesCartes() {
    document.querySelectorAll('.card').forEach((carte, index) => {
        initialiserCarte(carte, index);
    });
}

/**
 * Initialise une liste de cartes à partir de leurs identifiants
 * @param {string[]} ids
 */
export function initialiserCartesParIds(ids) {
    ids.forEach(id => {
        const carte = document.getElementById(id);
        if (carte) {
            initialiserCarte(carte);
        } else {
            console.warn(`Carte introuvable avec l'ID : ${id}`);
        }
    });
}

/**
 * Affiche ou masque toutes les cartes
 * @param {boolean} afficher
 */
export function basculerToutesLesCartes(afficher) {
    document.querySelectorAll('.card').forEach((carte, index) => {
        const el = obtenirElementsCarte(carte, index);
        if (el && el.icone) {
            basculerAffichage(el.contenu, el.icone, el.id, afficher);
        }
    });
}

/**
 * Réinitialise tous les états enregistrés
 */
export function reinitialiserEtatsCartes() {
    etats = {};
    localStorage.removeItem(CLE_STORAGE);
}

/**
 * Injecte les styles nécessaires (une seule fois)
 */
function injecterStylesIcones() {
    if (document.getElementById('stylesToggleCartes')) {
        return;
    }
    const style = document.createElement('style');
    style.id = 'stylesToggleCartes';
    style.textContent = `
        .iconeOuvrir::before {
            content: '▼';
        }
        .iconeFermer::before {
            content: '▲';
        }
        .float-end {
            float: right;
        }
        /* Classe pour arrondir le header seul (carte fermée) */
        .card-header.arrondi-seul {
            border-bottom-left-radius: 0.375rem; /* équivalent Bootstrap rounded */
            border-bottom-right-radius: 0.375rem;
        }
    `;
    document.head.appendChild(style);
}

// Injecte les styles au chargement du module
injecterStylesIcones();
