// noinspection JSUnusedGlobalSymbols

// Active le mode strict afin d'éviter les erreurs silencieuses
// et d'imposer un JS plus rigoureux.
'use strict';

// ============================================================================
// Version      : 2026.3
// Date         : 20/09/2026
// ============================================================================


/**
 * Crée une cellule de tableau (<td>) avec du texte ou du HTML.
 *
 * @param {string} contenu - Texte ou HTML à insérer.
 * @param {Object} [options]
 * @param {boolean} [options.centrer=false] - Centre le contenu horizontalement.
 * @param {boolean} [options.masquer=false] - Ajoute la classe "masquer".
 * @param {boolean} [options.isHTML=false] - Interprète le contenu comme du HTML.
 * @returns {HTMLTableCellElement}
 */
export function creerTd(
    contenu,
    {centrer = false, masquer = false, isHTML = false} = {}
) {
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
 * Crée une cellule de tableau (<td>) contenant une image.
 *
 * @param {string} src - Chemin ou URL de l'image.
 * @param {string} [alt=''] - Texte alternatif de l'image.
 * @param {Object} [options]
 * @param {number} [options.size=40] - Taille de l'image en pixels.
 * @param {string} [options.radius='50%'] - Rayon des bordures.
 * @param {boolean} [options.masquer=false] - Ajoute la classe "masquer".
 * @returns {HTMLTableCellElement}
 */
export function creerTdAvecImage(
    src,
    alt = '',
    {size = 40, radius = '50%', masquer = false} = {}
) {
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
 * Construit une ligne de tableau (<tr>) à partir d'un tableau de cellules.
 *
 * @param {HTMLTableCellElement[]} [lesTds=[]]
 * @returns {HTMLTableRowElement}
 */
export function creerTr(lesTds = []) {
    const tr = document.createElement('tr');

    tr.style.verticalAlign = 'middle';

    for (const td of lesTds) {
        tr.appendChild(td);
    }

    return tr;
}


// ============================================================================
// BOUTONS
// ============================================================================

/**
 * Crée dynamiquement un bouton d'action.
 *
 * @param {Object} options
 * @param {string} options.icone - Symbole affiché.
 * @param {string} [options.couleur='black'] - Couleur du symbole.
 * @param {string} [options.titre=''] - Info-bulle.
 * @param {function|null} [options.action=null] - Fonction appelée au clic.
 * @returns {HTMLButtonElement}
 */
export function creerBoutonAction({
                                      icone,
                                      couleur = 'black',
                                      titre = '',
                                      action = null
                                  }) {
    const bouton = document.createElement('button');

    bouton.type = 'button';
    bouton.textContent = icone;
    bouton.title = titre;


    Object.assign(bouton.style, {
        color: couleur,
        background: 'none',
        border: 'none',
        padding: '0',
        margin: '0',
        fontSize: '1em',
        userSelect: 'none',
        cursor: 'pointer',
        transition: 'transform 0.2s ease, box-shadow 0.2s ease',
        verticalAlign: 'middle',
        display: 'inline'
    });


    bouton.classList.add('bouton-action');

    injecterHoverStyleBouton();

    if (typeof action === 'function') {
        bouton.addEventListener('click', action);
    }

    return bouton;
}


function injecterHoverStyleBouton() {
    const styleId = 'style-bouton-action-hover';

    if (document.getElementById(styleId)) {
        return;
    }

    const style = document.createElement('style');

    style.id = styleId;

    style.textContent = `
        .bouton-action:hover {
            transform: scale(1.3);
        }
    `;

    document.head.appendChild(style);
}


/**
 * Crée un bouton de modification.
 *
 * @param {function} action
 * @returns {HTMLButtonElement}
 */
export function creerBoutonModification(action) {
    return creerBoutonAction({
        icone: '✎',
        couleur: 'orange',
        titre: 'Modifier l\'enregistrement',
        action: action
    });
}


/**
 * Crée un bouton de suppression.
 *
 * @param {function} action
 * @returns {HTMLButtonElement}
 */
export function creerBoutonSuppression(action) {
    return creerBoutonAction({
        icone: '✘',
        couleur: 'red',
        titre: 'Supprimer l\'enregistrement',
        action: action
    });
}


/**
 * Crée un bouton permettant de remplacer un document.
 *
 * @param {function} action
 * @returns {HTMLButtonElement}
 */
export function creerBoutonRemplacer(action) {
    return creerBoutonAction({
        icone: '♻️',
        couleur: 'red',
        titre: 'Téléverser une nouvelle version du document PDF',
        action: action
    });
}


// ============================================================================
// CHAMPS DE FORMULAIRE
// ============================================================================

/**
 * Crée un champ <input type="text">.
 *
 * @param {Object} [options]
 * @param {string} [options.value=''] - Valeur initiale.
 * @param {string} [options.placeholder=''] - Texte d'aide.
 * @param {string} [options.ariaLabel=''] - Libellé accessible.
 * @param {Array<string>} [options.classes=[]] - Classes CSS.
 * @param {string} [options.width=''] - Largeur CSS.
 * @param {boolean} [options.required=false] - Champ obligatoire.
 * @param {number|string} [options.maxLength] - Longueur maximale.
 * @param {number|string} [options.minLength] - Longueur minimale.
 * @param {string} [options.pattern] - Expression régulière de validation.
 * @returns {HTMLInputElement}
 */
export function creerInputTexte({
                                    value = '',
                                    placeholder = '',
                                    ariaLabel = '',
                                    classes = [],
                                    width = '',
                                    required = false,
                                    maxLength,
                                    minLength,
                                    pattern = ''
                                } = {}) {
    const input = document.createElement('input');

    input.type = 'text';
    input.value = value;
    input.placeholder = placeholder;

    input.classList.add(...classes);

    if (ariaLabel) {
        input.setAttribute('aria-label', ariaLabel);
    }

    if (width) {
        input.style.width = width;
    }

    if (required) {
        input.required = true;
    }

    if (maxLength !== undefined) {
        input.maxLength = maxLength;
    }

    if (minLength !== undefined) {
        input.minLength = minLength;
    }

    if (pattern) {
        input.pattern = pattern;
    }

    return input;
}


/**
 * Crée un champ <input type="date">.
 *
 * La valeur d'un input date est normalisée au format YYYY-MM-DD.
 *
 * @param {Object} [options]
 * @param {string} [options.value=''] - Date initiale.
 * @param {string} [options.min=''] - Date minimale.
 * @param {string} [options.max=''] - Date maximale.
 * @param {Array<string>} [options.classes=[]] - Classes CSS.
 * @param {string} [options.ariaLabel=''] - Libellé accessible.
 * @returns {HTMLInputElement}
 */
export function creerInputDate({
                                   value = '',
                                   min = '',
                                   max = '',
                                   classes = [],
                                   ariaLabel = ''
                               } = {}) {
    const input = document.createElement('input');

    input.type = 'date';
    input.value = value;

    input.classList.add(...classes);

    if (min) {
        input.min = min;
    }

    if (max) {
        input.max = max;
    }

    if (ariaLabel) {
        input.setAttribute('aria-label', ariaLabel);
    }

    return input;
}


/**
 * Crée un champ <input type="number">.
 *
 * @param {Object} [options]
 * @param {number|string} [options.value=''] - Valeur initiale.
 * @param {number|string} [options.min] - Valeur minimale.
 * @param {number|string} [options.max] - Valeur maximale.
 * @param {number|string} [options.step='1'] - Pas d'incrément.
 * @param {Array<string>} [options.classes=[]] - Classes CSS.
 * @param {string} [options.ariaLabel=''] - Libellé accessible.
 * @returns {HTMLInputElement}
 */
export function creerInputNombre({
                                     value = '',
                                     min,
                                     max,
                                     step = '1',
                                     classes = [],
                                     ariaLabel = ''
                                 } = {}) {
    const input = document.createElement('input');

    input.type = 'number';
    input.value = value;
    input.step = step;

    input.classList.add(...classes);

    if (min !== undefined) {
        input.min = min;
    }

    if (max !== undefined) {
        input.max = max;
    }

    if (ariaLabel) {
        input.setAttribute('aria-label', ariaLabel);
    }

    return input;
}


/**
 * Crée un champ <input type="email">.
 *
 * @param {Object} [options]
 * @param {string} [options.value=''] - Valeur initiale.
 * @param {string} [options.placeholder=''] - Texte d'aide.
 * @param {Array<string>} [options.classes=[]] - Classes CSS.
 * @param {string} [options.ariaLabel=''] - Libellé accessible.
 * @returns {HTMLInputElement}
 */
export function creerInputEmail({
                                    value = '',
                                    placeholder = '',
                                    classes = [],
                                    ariaLabel = ''
                                } = {}) {
    const input = document.createElement('input');

    input.type = 'email';
    input.value = value;
    input.placeholder = placeholder;

    input.classList.add(...classes);

    if (ariaLabel) {
        input.setAttribute('aria-label', ariaLabel);
    }

    return input;
}


/**
 * Crée un champ <input type="tel">.
 *
 * @param {Object} [options]
 * @param {string} [options.value=''] - Valeur initiale.
 * @param {string} [options.placeholder=''] - Texte d'aide.
 * @param {Array<string>} [options.classes=[]] - Classes CSS.
 * @param {string} [options.ariaLabel=''] - Libellé accessible.
 * @returns {HTMLInputElement}
 */
export function creerInputTelephone({
                                        value = '',
                                        placeholder = '',
                                        classes = [],
                                        ariaLabel = ''
                                    } = {}) {
    const input = document.createElement('input');

    input.type = 'tel';
    input.value = value;
    input.placeholder = placeholder;

    input.classList.add(...classes);

    if (ariaLabel) {
        input.setAttribute('aria-label', ariaLabel);
    }

    return input;
}


/**
 * Crée une case à cocher.
 *
 * @param {Object} [options]
 * @param {boolean} [options.checked=false] - État initial.
 * @param {string} [options.ariaLabel=''] - Libellé accessible.
 * @returns {HTMLInputElement}
 */
export function creerCheckbox({
                                  checked = false,
                                  ariaLabel = ''
                              } = {}) {
    const input = document.createElement('input');

    input.type = 'checkbox';
    input.checked = checked;

    input.classList.add('form-check-input', 'my-auto', 'm-3');

    input.style.width = '25px';
    input.style.height = '25px';

    if (ariaLabel) {
        input.setAttribute('aria-label', ariaLabel);
    }

    return input;
}


/**
 * Crée un élément <select>.
 *
 * @param {Array<string>} [classes=[]] - Classes CSS.
 * @returns {HTMLSelectElement}
 */
export function creerSelect(classes = []) {
    const select = document.createElement('select');

    select.classList.add(...classes);

    return select;
}