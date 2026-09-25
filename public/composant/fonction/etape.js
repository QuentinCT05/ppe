// noinspection JSUnusedGlobalSymbols

"use strict";

// Version 2026.1
// Date version : 11/08/2026

/**
 * Module générique de gestion d'étapes pour une interface utilisateur.
 *
 * Fonctionnalités principales :
 *   Affiche une seule étape visible à la fois
 *   Ajoute automatiquement des boutons de navigation "Précédent" et "Suivant"
 *   Affiche la progression "Étape X / Y" dynamiquement
 *   Appelle une fonction à la dernière étape si nécessaire (callback)
 *   Bloque la navigation si une étape ne passe pas la validation
 *
 * Utilisation recommandée :
 *   initialiserEtapes() → version simple, sans validation ni callback
 *   initialiserEtapes (finProcessus) → appel d’un callback uniquement à la dernière étape
 *   initialiserEtapes (null, [valider1, val2])→ validations par étape sans callback
 *   initialiserEtapes (fin, [val1, val2]) → validations + callback à la fin
 */

/**
 * Initialise la navigation multi-étapes.
 *
 * @param {Function|null} callbackDerniere - Fonction appelée quand la dernière étape est atteinte (facultatif)
 * @param {Function[]|null} validations - Tableau de fonctions de validation par étape (facultatif).
 *                                        Si l'une retourne false, le passage à l'étape suivante est bloqué.
 */
export function initialiserEtapes(callbackDerniere = null, validations = []) {
    // Sélectionne toutes les divs contenant la classe ".etape"
    const etapes = Array.from(document.querySelectorAll('.etape'));

    // Alerte si aucune étape n’est trouvée
    if (etapes.length === 0) {
        console.warn("Aucune <div class='etape'> trouvée dans le document.");
        return;
    }

    // Index de l’étape actuellement affichée (commence à 0)
    let etapeCourante = 0;

    /**
     * Affiche uniquement l’étape correspondant à l’index fourni.
     * Masque toutes les autres étapes.
     *
     * @param {number} index - Index de l’étape à afficher (0-based)
     */
    function afficherEtape(index) {
        for (let i = 0; i < etapes.length; i++) {
            // Seule l’étape courante est visible
            etapes[i].style.display = (i === index) ? 'block' : 'none';

            // Met à jour dynamiquement le texte de progression "Étape x / y"
            const progressionText = etapes[i].querySelector('.progression-etape');
            if (progressionText) {
                progressionText.textContent = `Étape ${index + 1} / ${etapes.length}`;
            }
        }

        // Met à jour l’étape courante
        etapeCourante = index;

        // Si on est sur la dernière étape, exécute le callback si défini
        if (etapeCourante === etapes.length - 1 && typeof callbackDerniere === 'function') {
            callbackDerniere();
        }
    }

    /**
     * Crée les boutons de navigation ("Précédent" et "Suivant") pour une étape donnée.
     *
     * @param {number} index - Index de l’étape courante
     * @returns {HTMLElement} - Élément <div> contenant les boutons
     */
    function creerBoutonsNavigation(index) {
        const container = document.createElement('div');
        container.className = 'd-flex gap-2';

        // Bouton "Précédent" (sauf à la première étape)
        if (index > 0) {
            const btnPrev = document.createElement('button');
            btnPrev.textContent = '◁ Précédent';
            btnPrev.className = 'btn btn-warning btn-sm';
            btnPrev.onclick = () => afficherEtape(index - 1);
            container.appendChild(btnPrev);
        } else {
            // Crée un espace vide pour équilibrer le layout
            container.appendChild(document.createElement('div'));
        }

        // Bouton "Suivant" (sauf à la dernière étape)
        if (index < etapes.length - 1) {
            const btnNext = document.createElement('button');
            btnNext.textContent = 'Suivant ▷';
            btnNext.className = 'btn btn-warning btn-sm';
            btnNext.onclick = () => {
                // Vérifie s’il y a une fonction de validation pour cette étape
                const valid = typeof validations[index] === 'function' ? validations[index]() : true;

                // Si la validation passe, on avance à l'étape suivante
                if (valid) {
                    afficherEtape(index + 1);
                }
            };
            container.appendChild(btnNext);
        } else {
            container.appendChild(document.createElement('div'));
        }

        return container;
    }

    // Injecte dans chaque étape une barre de navigation
    etapes.forEach((etape, index) => {
        const ligneNavigation = document.createElement('div');
        ligneNavigation.className = 'd-flex justify-content-between align-items-center mb-3';

        // Élément <div> affichant le texte "Étape X / Y"
        const progressionText = document.createElement('div');
        progressionText.className = 'progression-etape fw-bold';
        progressionText.textContent = `Étape ${index + 1} / ${etapes.length}`;
        ligneNavigation.appendChild(progressionText);

        // Ajoute les boutons Précédent/Suivant
        const boutons = creerBoutonsNavigation(index);
        ligneNavigation.appendChild(boutons);

        // Insère cette ligne tout en haut de l’étape
        etape.insertBefore(ligneNavigation, etape.firstChild);
    });

    // Affiche la première étape au chargement
    afficherEtape(0);
}
