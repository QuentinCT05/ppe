// noinspection JSUnusedGlobalSymbols

"use strict";

// Version 2026.1
// Date version : 11/08/2026

/**
 * retourne le message d'erreur associé à la réponse de l'API
 * @param reponse
 * @returns {string}
 */
export function getErrorAPI(reponse) {
    if (reponse === "Not Found") {
        return "Le point d'accès appelé n'existe pas";
    }
    if (reponse === "Bad credentials") {
        return "Vos paramètres d'authentification sont incorrects";
    }
    if (reponse === "Requires authentication") {
        return "Votre demande nécessite une authentification";
    }
    if (reponse === "Repository creation failed.") {
        return "La création du référentiel a échoué";
    }
    if (reponse === "name already exists on this account") {
        return "Le nom du référentiel est déjà utilisé";
    }
    if (reponse === "Body should be a JSON object") {
        return "Votre demande ne comporte pas les paramètres attendus";
    }
    return "Échec de la demande";
}

/**
 * Met en attente l'exécution du programme pendant le nombre de millisecondes passé en paramètre
 * @param {int} ms nombre de millisecondes
 * @returns {Promise<unknown>}
 */
export function wait(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}


/**
 * Vérifie si une classe CSS donnée est définie dans l'une des feuilles de style
 * accessibles du document.
 *
 * @param {string} nomClasse - Nom de la classe CSS à rechercher, sans le point initial.
 * @returns {boolean} true si une règle CSS utilise cette classe, sinon false.
 */
export function classeCssExiste(nomClasse) {
    const expression = new RegExp(`\\.${nomClasse}(?![a-zA-Z0-9_-])`);

    for (const feuille of document.styleSheets) {
        try {
            const regles = feuille.cssRules;

            for (const regle of regles) {
                if (regle.selectorText && expression.test(regle.selectorText)) {
                    return true;
                }
            }
        } catch (e) {
            // Feuille inaccessible, notamment pour des raisons de sécurité CORS.
        }
    }

    return false;
}

/**
 * Ajoute dynamiquement une classe CSS si elle n'existe pas encore.
 *
 * @param {string} nomClasse - Nom de la classe à ajouter (sans point)
 * @param {string} declarationCSS - Contenu de la règle CSS
 */
export function ajouterClasseCss(nomClasse, declarationCSS) {
    if (classeCssExiste(nomClasse)) {
        return;
    }

    const style = document.createElement('style');
    style.textContent = `.${nomClasse} { ${declarationCSS} }`;
    document.head.appendChild(style);
}
