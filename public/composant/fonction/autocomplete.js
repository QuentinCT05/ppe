// noinspection JSUnusedGlobalSymbols

"use strict";

// Version 2026.1
// Date version : 11/08/2026

/* global autoComplete */ // Indique à l'analyseur qu'autoComplete est une variable globale déjà disponible (importée dans la page HTML)

import { appelAjax } from "/composant/fonction/ajax.js"; // Import de ta fonction générique d'appel AJAX

/**
 * Initialise un champ de saisie avec autocomplétion.
 *
 * @param {Object} config - Configuration de l'autocomplétion
 * @param {string} config.selector - Sélecteur CSS du champ <input> concerné
 * @param {string} config.fetchUrl - URL du script PHP appelé via AJAX pour récupérer les résultats
 * @param {string} [config.searchKey="nomPrenom"] - Propriété utilisée dans les objets de résultat pour l'affichage et la recherche
 * @param {function} config.onSelection - Fonction callback exécutée quand un résultat est sélectionné
 *
 * @returns {autoComplete} - Instance de l'objet autoComplete
 */
export function initAutoComplete({ selector, fetchUrl, searchKey = "nomPrenom", onSelection }) {
    const options = {
        data: {
            // Fonction asynchrone qui appelle ton script PHP avec le terme recherché
            src: async (query) => {
                try {
                    const reponse = await appelAjax({
                        url: fetchUrl,
                        method: 'GET',
                        data: { search: query },
                        dataType: 'json'
                    });

                    // Sécurise : si la réponse n'est pas un tableau, on renvoie un tableau vide
                    return Array.isArray(reponse) ? reponse : [];
                } catch (error) {
                    console.error("Erreur lors de l'appel AJAX", error);
                    return [];
                }
            },
            keys: [searchKey] // Propriété sur laquelle porte la recherche dans chaque objet JSON
        },
        selector: selector, // Le champ HTML ciblé
        threshold: 1, // Nombre minimum de caractères avant de déclencher l'autocomplétion
        resultItem: {
            highlight: true, // Met en surbrillance la partie du mot qui correspond à la recherche
        },
        theme: 'round', // Style de présentation
        diacritics: true, // Autorise la recherche avec ou sans accent
        resultsList: {
            noResults: true, // Affiche un message si aucun résultat
            maxResults: 10,  // Limite le nombre de résultats affichés
            element: (list, data) => {
                // Fonction personnalisée pour afficher un message quand il n'y a aucun résultat
                if (!data.results.length) {
                    const info = document.createElement("p");
                    info.style.padding = "4px 6px";
                    info.style.fontStyle = "italic";
                    info.style.fontSize = "0.8em";
                    info.style.color = "#666";
                    info.textContent = "Aucune correspondance trouvée";
                    list.appendChild(info);
                }
            }
        },
        events: {
            input: {
                selection: (event) => {
                    // Quand l'utilisateur sélectionne une suggestion.
                    const selection = event.detail.selection.value;
                    if (typeof onSelection === "function") {
                        onSelection(selection); // Appel de la fonction callback fournie
                    }
                }
            }
        },
    };

    // Création et retour de l'instance autoComplete
    // noinspection JSPotentiallyInvalidConstructorUsage
    return new autoComplete(options);
}
