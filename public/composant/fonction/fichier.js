// noinspection JSUnusedGlobalSymbols

// Active le mode strict afin d'éviter les erreurs silencieuses et d'imposer un JS plus rigoureux.
'use strict';

// ============================================================================
// Version      : 2026.2
// Date         : 11/08/2026
// ============================================================================

// Import de fonctions utilitaires depuis un module externe

import {afficherSousLeChamp } from './afficher.js';
// Import de la fonction utilitaire pour convertir les octets
import {conversionOctet} from './format.js';


/**
 * Valide un fichier en vérifiant sa taille et son extension, et affiche éventuellement un message d'erreur.
 *
 * @param {File} file - Le fichier.

 * @param {number} maxSize - Taille maximale autorisée en octets.
 * @param {string[]} lesExtensions - Extensions autorisées (ex: ['pdf', 'docx']).
 * @returns {boolean} - true si le fichier est valide, false sinon.
 */
export function fichierValide(file, maxSize, lesExtensions) {
    let message = '';
    if (!file) {
        message = 'Aucun fichier transmis';
    } else if (maxSize && file.size > maxSize) {
        message = `La taille du fichier (${conversionOctet(file.size, 'Ko')}) dépasse la taille autorisée (${conversionOctet(maxSize, 'Ko')})`;
    } else if (lesExtensions && lesExtensions.length > 0) {
        const extension = file.name.split('.').pop().toLowerCase();
        if (!lesExtensions.includes(extension)) {
            message = `Extension ${extension} non acceptée`;
        }
    }
    if (message !== '') {
        afficherSousLeChamp('fichier', message);
        return false;
    }
    return true;
}

/**
 * Vérifie les dimensions d’une image si un contrôle est requis.
 *
 * @param {File} file - Le fichier image à tester.
 * @param {boolean} redimensionner - Indique si le contrôle des dimensions doit être ignoré.
 * @param {number} largeurMax - Largeur maximale autorisée en pixels (zéro si la largeur n'est pas contrôlée).
 * @param {number} hauteurMax - Hauteur maximale autorisée en pixels (zéro si la hauteur n'est pas contrôlée).

 * @param {Function} onSuccess - Callback exécuté si les dimensions sont valides. Reçoit le fichier et l'objet image.

 */
export function verifierImage(file, redimensionner, largeurMax = 0, hauteurMax = 0, onSuccess = null) {
    const img = new Image();
    img.src = URL.createObjectURL(file);
    img.onload = () => {
        let message = '';
        // Contrôle des dimensions uniquement si le redimensionnement n'est pas prévu
        if (!redimensionner) {
            // Vérification largeur et hauteur
            if (largeurMax > 0 && hauteurMax > 0) {
                if (img.width > largeurMax || img.height > hauteurMax) {
                    message = `Les dimensions de l'image (${img.width}×${img.height}) dépassent la limite autorisée (${largeurMax}×${hauteurMax})`;
                }
            }
            // Vérification largeur uniquement
            else if (largeurMax > 0) {
                if (img.width > largeurMax) {
                    message = `La largeur de l'image (${img.width}) dépasse la limite autorisée (${largeurMax})`;
                }
            }
            // Vérification hauteur uniquement
            else if (hauteurMax > 0) {
                if (img.height > hauteurMax) {
                    message = `La hauteur de l'image (${img.height}) dépasse la limite autorisée (${hauteurMax})`;
                }
            }
        }
        if (message !== '') {
            afficherSousLeChamp('fichier', message);
        } else {
            onSuccess?.(file, img);
        }
        // Nettoyage de l'URL temporaire
        URL.revokeObjectURL(img.src);
    };
    img.onerror = () => {
        afficherSousLeChamp('fichier', "Le fichier n'est pas une image valide.");
        // Nettoyage de l'URL temporaire en cas d'erreur
        URL.revokeObjectURL(img.src);
    };
}

