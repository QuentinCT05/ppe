"use strict";
// Version 2025.1
// Date version : 17/09/2025

/**
 * Crée une cellule de tableau (<td>) avec du texte ou du HTML.
 * @param {string} contenu - Texte ou HTML à insérer
 * @param {Object} options - Options de style
 * @param {boolean} options.centrer - Centre le contenu horizontalement
 * @param {boolean} options.masquer - Ajoute une classe "masquer"
 * @param {boolean} options.isHTML - Interprète le contenu comme du HTML
 * @returns {HTMLTableCellElement}
 */
export function getTd(contenu, { centrer = false, masquer = false, isHTML = false } = {}) {
    const td = document.createElement('td');
    if (isHTML) {
        td.innerHTML = contenu;
    } else {
        td.innerText = contenu;
    }
    if (centrer) {
        td.style.textAlign = 'center';
    }
    if (masquer) {
        td.classList.add('masquer');
    }
    return td;
}

/**
 * Crée une cellule contenant une image arrondie.
 * @param {string} src - Chemin de l'image
 * @param {string} alt - Texte alternatif pour l'image
 * @param {Object} options - Options de style
 * @param {number} options.size - Taille en pixels (hauteur = largeur)
 * @param {boolean} options.masquer - Ajoute la classe "masquer"
 * @returns {HTMLTableCellElement}
 */
export function getTdWithImg(src, alt = '', { size = 40, radius = '50%', masquer = false } = {}) {
    const td = document.createElement('td');
    const img = document.createElement('img');

    img.src = src;
    img.alt = alt;
    img.style.width = img.style.height = `${size}px`;
    img.style.borderRadius = radius;
    img.style.objectFit = 'cover';

    td.appendChild(img);

    if (masquer) {
        td.classList.add('masquer');
    }

    return td;
}

/**
 * Construit une ligne de tableau (<tr>) à partir d’un tableau de cellules.
 * @param {HTMLTableCellElement[]} lesTds - Cellules à insérer dans la ligne
 * @returns {HTMLTableRowElement}
 */
export function getTr(lesTds = []) {
    const tr = document.createElement('tr');
    tr.style.verticalAlign = 'middle';

    for (const td of lesTds) {
        tr.appendChild(td);
    }

    return tr;
}
