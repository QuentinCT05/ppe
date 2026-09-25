// noinspection JSUnusedGlobalSymbols

'use strict';

// Version 2026.1
// Date version : 11/08/2026

/**
 * Supprime les accents d'une chaîne de caractères
 * @param str
 * @returns {*}
 */
export function enleverAccentEtMajuscule(str) {
    return enleverAccent(str).toLowerCase();
}

/**
 * Compare 2 chaînes sans tenir compte des accents et de la casse
 * @param {string} str1 chaîne à comparer
 * @param {string} str2 chaîne à comparer
 * @returns {boolean} true si les 2 chaînes sont identiques
 */
export function comparerSansAccentEtSansCasse(str1, str2) {
    const x = enleverAccentEtMajuscule(str1);
    const y = enleverAccentEtMajuscule(str2);
    return x === y;
}

/**
 * Compare 2 chaînes sans tenir compte de la casse
 * @param {string} str1 chaîne à comparer
 * @param {string} str2 chaîne à comparer
 * @returns {boolean} true si les 2 chaînes sont identiques
 */
export function comparerSansCasse(str1, str2) {
    return str1.toLowerCase() === str2.toLowerCase();
}

/**
 * Vérifie si une chaîne de caractères contient une autre chaîne, sans tenir compte des accents et de la casse
 * @param texte
 * @param recherche
 * @returns {*}
 */
export function contenir(texte, recherche) {
    const txt = enleverAccentEtMajuscule(texte);
    const cherche = enleverAccentEtMajuscule(recherche);
    return txt.includes(cherche);
}

/**
 * Supprime les espaces superflus au début, à la fin et à l'intérieur.
 * @param {string} valeur
 * @returns {string}
 */
export function supprimerEspace(valeur) {
    return valeur.trim().replace(/\s+/g, ' ');
}

/**
 * Supprime les accents.
 * @param {string} valeur
 * @returns {string}
 */
export function enleverAccent(valeur) {
    return valeur
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');
}

/**
 * Convertit une chaîne en minuscules sans accents.
 * @param {string} valeur
 * @returns {string}
 */
export function normaliser(valeur) {
    return enleverAccent(valeur).toLowerCase().trim();
}

/**
 * Retourne la chaine passée en paramètre avec la première lettre de chaque mot en majuscule
 * @param {string} nom
 * @return {string} avec la première lettre de chaque mot en majuscule
 */
export function ucWord(nom) {
    // Vérifier si la chaîne est définie
    if (nom === undefined || nom === null) {
        return ''; // Retourner une chaîne vide si la chaîne est indéfinie ou nulle
    }
    // Vérifier si la chaîne n'est pas vide
    if (nom.length === 0) {
        return nom; // Retourner la chaîne vide si elle l'est
    }
    const lesMots = nom.trim().toLowerCase().split(/\s+/);
    for (let i = 0; i < lesMots.length; i += 1) {
        const lesSousMots = lesMots[i].split('-');
        for (let j = 0; j < lesSousMots.length; j += 1) {
            lesSousMots[j] = lesSousMots[j].charAt(0).toUpperCase() + lesSousMots[j].slice(1);
        }
        lesMots[i] = lesSousMots.join('-');
    }
    return lesMots.join(' ');
}

/**
 * Retourne la chaine passée en paramètre avec la première lettre en majuscule
 * @param nom
 * @returns {*|string}
 */
export function ucFirst(nom) {
    if (!nom) {
        return '';
    }
    nom = nom.trim();
    return nom.charAt(0).toUpperCase() + nom.slice(1).toLowerCase();
}

/**
 * Vérifie si une chaîne contient une autre chaîne.
 * Comparaison sans accents ni casse.
 * @param {string} texte
 * @param {string} recherche
 * @returns {boolean}
 */
export function contient(texte, recherche) {
    return normaliser(texte).includes(normaliser(recherche));
}

/**
 * Vérifie si une chaîne commence par une autre.
 * Sans accents ni casse.
 * @param {string} texte
 * @param {string} prefixe
 * @returns {boolean}
 */
export function commencePar(texte, prefixe) {
    return normaliser(texte).startsWith(normaliser(prefixe));
}

/**
 * Vérifie si une chaîne se termine par une autre.
 * Sans accents ni casse.
 * @param {string} texte
 * @param {string} suffixe
 * @returns {boolean}
 */
export function terminePar(texte, suffixe) {
    return normaliser(texte).endsWith(normaliser(suffixe));
}

/**
 * Compte le nombre d'occurrences d'une sous-chaîne.
 * @param {string} texte
 * @param {string} recherche
 * @returns {number}
 */
export function compterOccurrence(texte, recherche) {
    if (!recherche) {
        return 0;
    }

    return texte.split(recherche).length - 1;
}

/**
 * Tronque une chaîne et ajoute...
 * @param {string} texte
 * @param {number} longueur
 * @returns {string}
 */
export function tronquer(texte, longueur) {
    if (texte.length <= longueur) {
        return texte;
    }

    return texte.substring(0, longueur) + '...';
}

/**
 * Répète une chaîne.
 * @param {string} texte
 * @param {number} nombre
 * @returns {string}
 */
export function repeter(texte, nombre) {
    return texte.repeat(nombre);
}

/**
 * Supprime tous les espaces.
 * @param {string} texte
 * @returns {string}
 */
export function supprimerTousLesEspaces(texte) {
    return texte.replace(/\s+/g, '');
}

/**
 * Conserve uniquement les chiffres.
 * @param {string} texte
 * @returns {string}
 */
export function chiffresSeulement(texte) {
    return texte.replace(/\D/g, '');
}

/**
 * Conserve uniquement les lettres.
 * @param {string} texte
 * @returns {string}
 */
export function lettresSeulement(texte) {
    return texte.replace(/[^a-zA-ZÀ-ÿ]/g, '');
}

/**
 * Vérifie si la chaîne est vide ou composée uniquement d'espaces.
 * @param {string} texte
 * @returns {boolean}
 */
export function estVide(texte) {
    return texte.trim() === '';
}

/**
 * Inverse les caractères d'une chaîne.
 * @param {string} texte
 * @returns {string}
 */
export function inverser(texte) {
    return [...texte].reverse().join('');
}

