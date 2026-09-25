'use strict';

/**
 * bibliothèque de fonctions facilitant l'insertion d'icônes au format svg
 *
 * @Author : Guy Verghote
 * @Version 2025.1
 */

// Fonction utilitaire
// Crée une icône SVG en utilisant un <use> dans un sprite externe
export function creerIconeDepuisSprite({nom, taille = 32, couleur = 'currentColor', titre = null}) {
    const ns = 'http://www.w3.org/2000/svg';

    const svg = document.createElementNS(ns, 'svg');
    svg.setAttribute('width', taille);
    svg.setAttribute('height', taille);
    svg.setAttribute('fill', couleur);
    svg.setAttribute('aria-hidden', 'true');
    svg.setAttribute('role', 'img');

    if (titre) {
        const title = document.createElementNS(ns, 'title');
        title.textContent = titre;
        svg.appendChild(title);
    }

    const use = document.createElementNS(ns, 'use');
    use.setAttribute('href', `/composant/icone/icone.svg?v=2#${nom}`);
    svg.appendChild(use);

    svg.style.display = 'block';

    return svg;
}

// Icône PDF
export function iconePdf({taille = 32, couleur = 'red', titre = 'Afficher le PDF'} = {}) {
    return creerIconeDepuisSprite({
        nom: 'pdf',
        taille: taille,
        couleur: couleur,
        titre: titre
    });
}

export function iconeSupprimer({taille = 32, couleur = 'red', titre = 'Supprimer l\'élément'} = {}) {
    return creerIconeDepuisSprite({
        nom: 'supprimer',
        taille: taille,
        couleur: couleur,
        titre: titre
    });
}

export function iconeAjouter({taille = 32, couleur = 'red'} = {}) {
    return creerIconeDepuisSprite({nom: 'ajouter', taille: taille, couleur: couleur});
}

export function iconePoubelle({taille = 32, couleur = 'red'} = {}) {
    return creerIconeDepuisSprite({nom: 'poubelle', taille, couleur});
}

export function iconeUpload({taille = 32, couleur = 'red'} = {}) {
    return creerIconeDepuisSprite({nom: 'upload', taille, couleur});
}

export function iconeManquant({taille = 32, couleur = 'red'} = {}) {
    return creerIconeDepuisSprite({nom: 'manquant', taille, couleur});
}

export function iconeRemplacer({taille = 32, couleur = 'red', titre = 'Remplacer le PDF'} = {}) {
    return creerIconeDepuisSprite({
        nom: 'remplacer',
        taille: taille,
        couleur: couleur,
        titre: titre
    });
}

export function iconeOeil({taille = 24, couleur = 'black', titre = 'Afficher le mot de passe'} = {}) {
    return creerIconeDepuisSprite({nom: 'oeil', taille, couleur, titre});
}

export function iconeOeilBarre({taille = 24, couleur = 'black', titre = 'Masquer le mot de passe'} = {}) {
    return creerIconeDepuisSprite({nom: 'oeil-barre', taille, couleur, titre});
}

