// noinspection JSUnusedGlobalSymbols

"use strict";

// Version 2026.1
// Date version : 08/08/2026

// ------------------------------------------------------------
// Fonction de calcul portant sur le temps et la distance
// ------------------------------------------------------------

/**
 * Convertit en temps hh:mm:sss en nombre de secondes
 * @param {string} temps
 * @return {number}
 */
function getNbSeconde(temps) {
    const lesValeurs = temps.split(':');
    // Convertir le temps en secondes
    const heures = parseInt(lesValeurs[0]);
    const minutes = parseInt(lesValeurs[1]);
    const secondes = parseInt(lesValeurs[2]);
    return heures * 3600 + minutes * 60 + secondes;
}

/**
 * Retourne la vitesse en km/h
 * @param {string} temps
 * @param {int} distance en km
 * @return {number}
 */
export function getVitesse(temps, distance) {
    const nbSeconde = getNbSeconde(temps);
    // formule de la vitesse : vitesse = distance / (nbSeconde / 3600)
    return 3600 * (distance) / nbSeconde;
}

/**
 * Retourne l'allure au kilomètre en minutes et en secondes
 * formule de l'allure en seconde : Math.floor(3600 / vitesse)
 * cependant on veut l'exprimer en minutes et en secondes
 * @param {string} temps
 * @param {int}distance
 * @return {string}
 */
export function getAllure(temps, distance) {

    const nbSeconde = Math.floor(3600 / getVitesse(temps, distance));
    // décomposition en minutes et secondes
    let mn = Math.floor(nbSeconde / 60);
    let se = nbSeconde - mn * 60;
    let seT = '0' + se;
    return mn.toString() + ':' + seT.slice(-2);
}
