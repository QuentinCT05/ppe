/* jshint esversion: 11, browser: true */
// noinspection JSUnusedGlobalSymbols

'use strict';

// Version 2026.4
// Date version : 06/09/2026

// -----------------------------------------------------------------------------
// Variables persistantes
// -----------------------------------------------------------------------------

let modal = null;
let btnOui = null;
let btnNon = null;
let confirmationMessage = null;


// -----------------------------------------------------------------------------
// Gestion globale des boutons de fermeture des messages
// -----------------------------------------------------------------------------
//
// Cette délégation permet de fermer les messages générés par genererMessage()
// sans dépendre de Bootstrap.
//
// Elle fonctionne également pour les messages ajoutés dynamiquement
// après le chargement de cette bibliothèque.
//

document.addEventListener('click', (event) => {

    const bouton = event.target.closest('.messageGenerer-fermer');

    if (!bouton) {
        return;
    }

    const message = bouton.closest('.messageGenerer');

    if (message) {
        message.remove();
    }
});


// -----------------------------------------------------------------------------
// Fermeture des boîtes de dialogue avec la touche Échap
// -----------------------------------------------------------------------------

document.addEventListener('keydown', (event) => {

    if (event.key !== 'Escape') {
        return;
    }

    // MessageBox
    document.getElementById('avertir-modal')?.remove();

    // Modale de confirmation
    if (modal) {
        modal.style.display = 'none';
    }

    // Modale de chargement
    const modalChargement =
        document.getElementById('modalVeuillezPatienter');

    if (modalChargement) {
        modalChargement.remove();
    }
});


// =============================================================================
// MESSAGES
// =============================================================================

/**
 * Génère un message d'information ou d'erreur.
 *
 * Cette fonction ne dépend pas de Bootstrap.
 *
 * @param {*} texte Texte à afficher.
 * @param {string} couleur Couleur : vert, rouge ou orange.
 * @param {number} duree Durée d'affichage en millisecondes.
 *                      0 = fermeture manuelle.
 *
 * @return {string} Chaîne HTML.
 */
export function genererMessage(texte, couleur = 'rouge', duree = 0) {

    // -------------------------------------------------------------------------
    // Sécurisation du texte
    // -------------------------------------------------------------------------

    if (texte === null || texte === undefined) {
        texte = '';
    } else if (typeof texte !== 'string') {
        texte = String(texte);
    }

    // -------------------------------------------------------------------------
    // Détermination de la couleur
    // -------------------------------------------------------------------------

    let code = '#fb7e7b';

    switch (couleur) {

        case 'vert':
            code = '#1FA055';
            break;

        case 'orange':
            code = '#FF7415';
            break;

        case 'rouge':
        default:
            code = '#fb7e7b';
            break;
    }

    // -------------------------------------------------------------------------
    // Génération d'un identifiant unique
    // -------------------------------------------------------------------------

    const id =
        `message-${Date.now()}-${Math.floor(Math.random() * 100000)}`;

    // -------------------------------------------------------------------------
    // Génération du HTML
    // -------------------------------------------------------------------------

    const html = `
        <div id="${id}"
             class="messageGenerer"
             role="alert"
             aria-live="assertive"
             aria-atomic="true"
             style="
                 position: relative;
                 color: white;
                 background-color: ${code};
                 font-size: 0.8rem;
                 font-style: italic;
                 padding: 0.5rem 2.5rem 0.5rem 0.75rem;
                 margin-bottom: 0.5rem;
                 border-radius: 0.25rem;
             ">

            ${texte}

            <button type="button"
                    class="messageGenerer-fermer"
                    aria-label="Fermer"
                    title="Fermer"
                    style="
                        position: absolute;
                        top: 50%;
                        right: 0.5rem;
                        transform: translateY(-50%);
                        border: 0;
                        background: transparent;
                        color: white;
                        font-size: 1.2rem;
                        line-height: 1;
                        cursor: pointer;
                        padding: 0.25rem;
                    ">
                ×
            </button>

        </div>
    `;

    // -------------------------------------------------------------------------
    // Fermeture automatique
    // -------------------------------------------------------------------------

    if (duree > 0) {

        setTimeout(() => {

            const element = document.getElementById(id);

            if (element) {
                element.remove();
            }

        }, duree);
    }

    return html;
}


// =============================================================================
// TOAST
// =============================================================================

/**
 * Affiche un toast personnalisé sur l'écran.
 *
 * Ne dépend pas de Bootstrap.
 *
 * @param {string} message
 * @param {string} type success, error, warning ou info
 * @param {string} position
 * @param {number} duration Durée en millisecondes.
 */
export function afficherToast(message, type = 'success', position = 'bottom-center', duration = 1500) {

    // -------------------------------------------------------------------------
    // Sécurisation
    // -------------------------------------------------------------------------

    if (message === null || message === undefined) {
        message = '';
    } else if (typeof message !== 'string') {
        message = String(message);
    }

    // -------------------------------------------------------------------------
    // Conteneur
    // -------------------------------------------------------------------------

    let container =
        document.getElementById('toast-container');

    if (!container) {

        container = document.createElement('div');

        container.id = 'toast-container';

        Object.assign(container.style, {
            position: 'fixed',
            zIndex: '9999',
            pointerEvents: 'none'
        });

        document.body.appendChild(container);
    }

    // -------------------------------------------------------------------------
    // Réinitialisation des positions
    // -------------------------------------------------------------------------

    container.style.top = '';
    container.style.bottom = '';
    container.style.left = '';
    container.style.right = '';
    container.style.transform = '';

    // -------------------------------------------------------------------------
    // Position
    // -------------------------------------------------------------------------

    switch (position) {

        case 'top-left':
            container.style.top = '1rem';
            container.style.left = '1rem';
            break;

        case 'top-right':
            container.style.top = '1rem';
            container.style.right = '1rem';
            break;

        case 'bottom-left':
            container.style.bottom = '1rem';
            container.style.left = '1rem';
            break;

        case 'bottom-right':
            container.style.bottom = '1rem';
            container.style.right = '1rem';
            break;

        case 'top-center':
            container.style.top = '1rem';
            container.style.left = '50%';
            container.style.transform = 'translateX(-50%)';
            break;

        case 'bottom-center':
            container.style.bottom = '1rem';
            container.style.left = '50%';
            container.style.transform = 'translateX(-50%)';
            break;

        case 'center':
            container.style.top = '50%';
            container.style.left = '50%';
            container.style.transform =
                'translate(-50%, -50%)';
            break;

        default:

            console.warn(
                `Position "${position}" inconnue, ` +
                `positionnement par défaut à "bottom-right".`
            );

            container.style.bottom = '1rem';
            container.style.right = '1rem';
            break;
    }

    // -------------------------------------------------------------------------
    // Couleurs
    // -------------------------------------------------------------------------

    const backgroundColors = {

        success: '#20ef51',
        error: '#ef2b3d',
        warning: '#fec105',
        info: '#47d9f4'
    };

    const textColors = {

        success: '#155724',
        error: '#721c24',
        warning: '#856404',
        info: '#0c5460'
    };

    // -------------------------------------------------------------------------
    // Création du toast
    // -------------------------------------------------------------------------

    const toast = document.createElement('div');

    toast.innerHTML = message;

    Object.assign(toast.style, {

        background:
            backgroundColors[type] || backgroundColors.info,

        color:
            textColors[type] || textColors.info,

        borderRadius: '5px',

        padding: '10px 15px',

        marginTop: '8px',

        boxShadow:
            '0 2px 6px rgba(0,0,0,0.2)',

        fontFamily: 'sans-serif',

        fontSize: '0.95rem',

        maxWidth: '300px',

        pointerEvents: 'auto',

        opacity: '1',

        transition: 'opacity 0.5s ease'
    });

    container.appendChild(toast);

    // -------------------------------------------------------------------------
    // Fermeture automatique
    // -------------------------------------------------------------------------

    if (duration > 0) {

        setTimeout(() => {

            toast.style.opacity = '0';

            setTimeout(() => {

                toast.remove();

                // Supprimer le conteneur s'il est devenu vide
                if (
                    container.children.length === 0 &&
                    container.parentElement
                ) {
                    container.remove();
                }

            }, 500);

        }, duration);
    }
}

// =============================================================================
// STYLE DES MESSAGES SOUS LES CHAMPS
// =============================================================================

/**
 * Installe le style global des messages d'erreur sous les champs.
 *
 * Le style est ajouté une seule fois dans <head>.
 */
function installerStyleMessageErreur() {

    if (document.getElementById('style-message-erreur')) {
        return;
    }

    const style = document.createElement('style');

    style.id = 'style-message-erreur';

    style.textContent = `
        .messageErreur {
            display: block;
            width: 100%;
            box-sizing: border-box;

            margin-top: 0.35rem;

            color: #dc3545;
            font-size: 0.8rem;
            font-weight: 500;
            line-height: 1.3;

            background: transparent;

            text-align: left;
        }

        .messageErreur::before {
            content: "⚠ ";
        }

        input[aria-invalid="true"],
        textarea[aria-invalid="true"],
        select[aria-invalid="true"] {
            border-color: #dc3545;
        }

        input[aria-invalid="true"]:focus,
        textarea[aria-invalid="true"]:focus,
        select[aria-invalid="true"]:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.15rem rgba(220, 53, 69, 0.15);
        }
    `;

    document.head.appendChild(style);
}


// =============================================================================
// MESSAGE SOUS UN CHAMP
// =============================================================================

/**
 * Affiche un message d'erreur sous un champ de formulaire.
 *
 * Le message est placé :
 * - sous .input-recherche pour les champs de recherche ;
 * - après .champ pour les champs classiques ;
 * - après .password-wrapper pour les champs avec mot de passe.
 *
 * @param {string|HTMLInputElement|HTMLTextAreaElement|HTMLSelectElement} inputOuId
 * @param {string|null} [message]
 */
export function afficherSousLeChamp(inputOuId, message) {

    // -------------------------------------------------------------------------
    // Récupération du champ
    // -------------------------------------------------------------------------

    let input;

    if (typeof inputOuId === 'string') {

        input = document.getElementById(inputOuId);

    } else if (
        inputOuId instanceof HTMLInputElement ||
        inputOuId instanceof HTMLTextAreaElement ||
        inputOuId instanceof HTMLSelectElement
    ) {

        input = inputOuId;
    }

    installerStyleMessageErreur();

    // -------------------------------------------------------------------------
    // Champ inexistant
    // -------------------------------------------------------------------------

    if (!input) {

        const nomChamp =
            typeof inputOuId === 'string'
                ? inputOuId
                : '(élément invalide)';

        const texteErreur =
            message ||
            `Erreur : Le champ "${nomChamp}" n'existe pas.`;

        const msg = document.getElementById('msg');

        if (msg) {

            msg.innerHTML = genererMessage(
                texteErreur,
                'rouge'
            );

        } else {

            messageBox(
                texteErreur,
                'error'
            );
        }

        console.warn(
            `afficherSousLeChamp : Le champ "${nomChamp}" est introuvable ou invalide.`
        );

        return;
    }

    // -------------------------------------------------------------------------
    // Suppression d'une ancienne erreur
    // -------------------------------------------------------------------------

    effacerSousLeChamp(input);

    // -------------------------------------------------------------------------
    // null = uniquement effacer l'erreur
    // -------------------------------------------------------------------------

    if (message === null) {
        return;
    }

    // -------------------------------------------------------------------------
    // undefined = utiliser le message HTML5
    // -------------------------------------------------------------------------

    if (message === undefined) {
        message = input.validationMessage;
    }

    // -------------------------------------------------------------------------
    // Aucun message
    // -------------------------------------------------------------------------

    if (!message) {
        return;
    }

    // -------------------------------------------------------------------------
    // Recherche du conteneur logique du champ
    // -------------------------------------------------------------------------

    /*
     * Pour une recherche :
     *
     * .champ-recherche
     *     └── .input-recherche
     *             ├── input
     *             └── .icone-recherche
     *
     * Le message doit être placé après .input-recherche,
     * jamais à l'intérieur.
     */

    const champRecherche = input.closest('.champ-recherche');

    // -------------------------------------------------------------------------
    // Identifiant unique du message
    // -------------------------------------------------------------------------

    const idMessage = input.id
        ? `${input.id}-erreur`
        : `erreur-${crypto.randomUUID()}`;

    // -------------------------------------------------------------------------
    // Création du message
    // -------------------------------------------------------------------------

    const nouvelleDiv = document.createElement('div');

    nouvelleDiv.id = idMessage;

    nouvelleDiv.className = 'messageErreur';

    nouvelleDiv.setAttribute('role', 'alert');

    nouvelleDiv.setAttribute('aria-live', 'polite');

    nouvelleDiv.innerText = message;

    // -------------------------------------------------------------------------
    // Insertion
    // -------------------------------------------------------------------------

    if (champRecherche) {

        /*
         * Cas d'un champ de recherche.
         *
         * On recherche le conteneur qui contient
         * réellement l'input et l'icône.
         */

        const inputRecherche =
            input.closest('.input-recherche');

        if (inputRecherche) {

            /*
             * Le message est placé APRÈS .input-recherche.
             *
             * Ainsi :
             *
             * .input-recherche
             *     ├── input
             *     └── icône
             *
             * .messageErreur
             *
             * L'icône reste donc centrée dans le champ.
             */

            inputRecherche.insertAdjacentElement(
                'afterend',
                nouvelleDiv
            );

        } else {

            /*
             * Sécurité pour les structures particulières.
             */

            champRecherche.appendChild(
                nouvelleDiv
            );
        }

    } else {

        // ---------------------------------------------------------------------
        // Champ classique hors zone de recherche
        // ---------------------------------------------------------------------

        const parent = input.parentElement;

        if (
            parent &&
            parent.classList.contains('password-wrapper')
        ) {

            /*
             * Cas d'un champ mot de passe.
             */

            if (
                parent.parentElement &&
                parent.parentElement.classList.contains('champ')
            ) {

                parent.parentElement.insertAdjacentElement(
                    'afterend',
                    nouvelleDiv
                );

            } else {

                parent.insertAdjacentElement(
                    'afterend',
                    nouvelleDiv
                );
            }

        } else if (
            parent &&
            parent.classList.contains('champ')
        ) {

            /*
             * Cas d'un champ dans .champ.
             */

            parent.insertAdjacentElement(
                'afterend',
                nouvelleDiv
            );

        } else {

            /*
             * Cas général.
             */

            input.insertAdjacentElement(
                'afterend',
                nouvelleDiv
            );
        }
    }

    // -------------------------------------------------------------------------
    // Accessibilité
    // -------------------------------------------------------------------------

    input.setAttribute(
        'aria-invalid',
        'true'
    );

    input.setAttribute(
        'aria-describedby',
        idMessage
    );
}



// =============================================================================
// EFFACER ERREUR SOUS UN CHAMP
// =============================================================================

/**
 * Supprime le message d'erreur associé à un champ.
 *
 * @param {string|HTMLInputElement|HTMLTextAreaElement|HTMLSelectElement} inputOuId
 */
export function effacerSousLeChamp(inputOuId) {

    let input;

    if (typeof inputOuId === 'string') {
        input = document.getElementById(inputOuId);
    } else if (inputOuId instanceof HTMLInputElement || inputOuId instanceof HTMLTextAreaElement || inputOuId instanceof HTMLSelectElement) {
        input = inputOuId;
    }
    if (!input) {
        return;
    }

    // -------------------------------------------------------------------------
    // Recherche du message associé
    // -------------------------------------------------------------------------

    const idMessage = input.getAttribute('aria-describedby');

    if (idMessage) {
        const messageErreur = document.getElementById(idMessage);
        if (messageErreur && messageErreur.classList.contains('messageErreur')) {
            messageErreur.remove();
        }
        input.removeAttribute('aria-describedby');
    }

    // -------------------------------------------------------------------------
    // Champ redevenu valide
    // -------------------------------------------------------------------------

    input.removeAttribute('aria-invalid');
}


// =============================================================================
// MESSAGE BOX
// =============================================================================

/**
 * Affiche une boîte de dialogue.
 *
 * @param {string} message
 * @param {string} type success, error, warning ou info
 */
export function messageBox(message, type = 'success') {

    // -------------------------------------------------------------------------
    // Sécurisation
    // -------------------------------------------------------------------------

    if (message === null || message === undefined) {
        message = '';
    }

    if (typeof message !== 'string') {
        message = String(message);
    }

    // -------------------------------------------------------------------------
    // Supprimer une ancienne boîte
    // -------------------------------------------------------------------------

    document.getElementById('avertir-modal')?.remove();

    // -------------------------------------------------------------------------
    // Couleurs
    // -------------------------------------------------------------------------

    const couleurs = {
        success: '#d4edda',
        error: '#f8d7da',
        warning: '#fff3cd',
        info: '#d1ecf1'
    };

    const textes = {
        success: '#155724',
        error: '#721c24',
        warning: '#856404',
        info: '#0c5460'
    };

    // -------------------------------------------------------------------------
    // Overlay
    // -------------------------------------------------------------------------

    const overlay =
        document.createElement('div');

    overlay.id = 'avertir-modal';

    Object.assign(overlay.style, {

        position: 'fixed',
        top: '0',
        left: '0',
        width: '100vw',
        height: '100vh',
        backgroundColor:
            'rgba(0, 0, 0, 0.5)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        zIndex: '9999',
        padding: '1rem'
    });

    // -------------------------------------------------------------------------
    // Boîte
    // -------------------------------------------------------------------------

    const box = document.createElement('div');

    Object.assign(box.style, {

        backgroundColor: couleurs[type] || couleurs.info,
        color: textes[type] || textes.info,
        padding: '20px',
        borderRadius: '8px',
        minWidth: '300px',
        maxWidth: '600px',
        width: '100%',
        boxShadow: '0 0 10px rgba(0,0,0,0.3)',
        fontFamily: 'sans-serif'
    });

    // -------------------------------------------------------------------------
    // Contenu
    // -------------------------------------------------------------------------

    const contenu = document.createElement('div');
    contenu.innerHTML = message;
    box.appendChild(contenu);

    // -------------------------------------------------------------------------
    // Bouton
    // -------------------------------------------------------------------------

    const bouton =
        document.createElement('button');

    bouton.type = 'button';
    bouton.textContent = 'OK';
    Object.assign(bouton.style, {
        marginTop: '15px',
        float: 'right',
        padding: '6px 12px',
        backgroundColor: '#ccc',
        border: 'none',
        borderRadius: '4px',
        cursor: 'pointer'
    });

    bouton.addEventListener('click', () => overlay.remove());
    box.appendChild(bouton);
    overlay.appendChild(box);
    document.body.appendChild(overlay);

    // -------------------------------------------------------------------------
    // Donner immédiatement le focus au bouton
    // -------------------------------------------------------------------------
    bouton.focus();
}


// =============================================================================
// AFFICHAGE D'ERREUR DANS LA CONSOLE
// =============================================================================

/**
 * Affiche un message d'erreur dans la console.
 *
 * @param {string|object|null|undefined} message
 */
export function afficherErreur(message) {

    let texte;

    // -------------------------------------------------------------------------
    // Objet ou tableau
    // -------------------------------------------------------------------------

    if (typeof message === 'object' && message !== null) {
        try {
            texte = JSON.stringify(message, null, 2);
        } catch {
            texte = String(message);
        }
    } else {

        // ---------------------------------------------------------------------
        // null / undefined
        // ---------------------------------------------------------------------

        if (message === null || message === undefined) {
            texte = '';
        } else {
            texte = String(message);
        }

        // ---------------------------------------------------------------------
        // Remplacement des <br>
        // ---------------------------------------------------------------------

        texte = texte.replace(/<br\s*\/?>/gi, "\n");

        // ---------------------------------------------------------------------
        // Suppression des autres balises HTML
        // ---------------------------------------------------------------------

        const tmp = document.createElement('div');
        tmp.innerHTML = texte;
        texte = tmp.textContent || tmp.innerText || '';
    }
    console.error("Erreur reçue :\n" + texte);
}


// =============================================================================
// REDIRECTION AUTOMATIQUE
// =============================================================================

/**
 * Affiche un message puis redirige automatiquement.
 *
 * @param {string} message
 * @param {string} url
 * @param {number} delay
 */
export function retournerVers(message, url, delay = 2000) {
    afficherToast(message, 'success');
    setTimeout(() => {
        location.href = url;
    }, delay);
}


// =============================================================================
// REDIRECTION APRES CONFIRMATION
// =============================================================================

/**
 * Affiche un message et attend confirmation avant redirection.
 *
 * @param {string} message
 * @param {string} url
 */
export function retournerVersApresConfirmation(message, url) {
    messageBox(
        message,
        'info'
    );

    const bouton =
        document.querySelector(
            '#avertir-modal button'
        );

    if (bouton) {
        bouton.onclick = () => {
            document.getElementById('avertir-modal')?.remove();
            location.href = url;
        };
    }
}


// =============================================================================
// CORRIGER
// =============================================================================

/**
 * Affiche une erreur et restaure la valeur d'un champ.
 *
 * @param {HTMLInputElement} input
 * @param {string} message
 * @param {?string} oldValue
 */
export function corriger(input, message = input.validationMessage, oldValue = null) {
    messageBox(message, 'error');
    if (input.type === 'checkbox') {
        input.checked = !input.checked;
    } else if (input.hasAttribute('data-old')) {
        input.value = input.dataset.old;
    } else {
        input.value = oldValue ?? '';
    }
}

// =============================================================================
// CONFIRMATION
// =============================================================================

/**
 * Affiche une modale de confirmation.
 *
 * @param {Function} callback
 * @param {string} message
 */
export function confirmer(callback, message = 'Confirmer votre demande ?') {

    // -------------------------------------------------------------------------
    // Création de la modale
    // -------------------------------------------------------------------------
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'confirmationModal';
        Object.assign(modal.style, {
            position: 'fixed',
            top: '0',
            left: '0',
            right: '0',
            bottom: '0',
            backgroundColor: 'rgba(0,0,0,0.6)',
            zIndex: '10000',
            display: 'none',
            alignItems: 'center',
            justifyContent: 'center',
            padding: '1rem'
        });

        // ---------------------------------------------------------------------
        // Contenu
        // ---------------------------------------------------------------------

        const contenu = document.createElement('div');

        Object.assign(contenu.style, {
            background: 'white',
            padding: '20px',
            borderRadius: '8px',
            maxWidth: '400px',
            width: '100%',
            textAlign: 'center',
            boxShadow:
                '0 0 15px rgba(0,0,0,0.3)'
        });

        // ---------------------------------------------------------------------
        // Message
        // ---------------------------------------------------------------------

        confirmationMessage = document.createElement('p');
        confirmationMessage.id = 'confirmationMessage';

        // ---------------------------------------------------------------------
        // Boutons
        // ---------------------------------------------------------------------

        const boutons = document.createElement('div');
        boutons.style.marginTop = '20px';
        btnOui = document.createElement('button');
        btnOui.type = 'button';
        btnOui.id = 'btnConfirmer';
        btnOui.className = 'btn-confirmation-oui';
        btnOui.textContent = 'Oui';
        Object.assign(btnOui.style, {
            marginRight: '0.5rem',
            padding: '0.5rem 1rem',
            backgroundColor: '#198754',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            cursor: 'pointer'
        });

        btnNon = document.createElement('button');
        btnNon.type = 'button';
        btnNon.id = 'btnAnnuler';
        btnNon.className = 'btn-confirmation-non';
        btnNon.textContent = 'Non';
        Object.assign(btnNon.style, {
            padding: '0.5rem 1rem',
            backgroundColor: '#dc3545',
            color: 'white',
            border: 'none',
            borderRadius: '4px',
            cursor: 'pointer'
        });

        boutons.appendChild(btnOui);
        boutons.appendChild(btnNon);
        contenu.appendChild(confirmationMessage);
        contenu.appendChild(boutons);
        modal.appendChild(contenu);
        document.body.appendChild(modal);

        // ---------------------------------------------------------------------
        // Bouton Non
        // ---------------------------------------------------------------------

        btnNon.onclick = () => {
            modal.style.display = 'none';
        };
    }

    // -------------------------------------------------------------------------
    // Mise à jour du message
    // -------------------------------------------------------------------------

    confirmationMessage.textContent = message;

    // -------------------------------------------------------------------------
    // Affichage
    // -------------------------------------------------------------------------

    modal.style.display = 'flex';

    // -------------------------------------------------------------------------
    // Événement Oui
    // -------------------------------------------------------------------------

    btnOui.onclick = () => {
        modal.style.display = 'none';
        if (typeof callback === 'function') {
            callback();
        }
    };

    // -------------------------------------------------------------------------
    // Focus sur Oui
    // -------------------------------------------------------------------------

    btnOui.focus();
}


// =============================================================================
// MODALE « VEUILLEZ PATIENTER »
// =============================================================================

/**
 * Affiche une fenêtre modale de chargement.
 *
 * Cette version ne dépend plus de Bootstrap.
 *
 * @returns {Promise<HTMLElement>}
 */
export async function afficherVeuillezPatienter() {

    // -------------------------------------------------------------------------
    // Vérifier si la fenêtre existe
    // -------------------------------------------------------------------------

    let modalChargement =
        document.getElementById(
            'modalVeuillezPatienter'
        );

    if (!modalChargement) {

        // ---------------------------------------------------------------------
        // Overlay
        // ---------------------------------------------------------------------

        modalChargement = document.createElement('div');
        modalChargement.id = 'modalVeuillezPatienter';

        Object.assign(modalChargement.style,
            {
                position: 'fixed',
                top: '0',
                left: '0',
                width: '100vw',
                height: '100vh',
                backgroundColor: 'rgba(0,0,0,0.5)',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                zIndex: '10000',
                padding: '1rem'
            }
        );

        // ---------------------------------------------------------------------
        // Contenu
        // ---------------------------------------------------------------------

        const contenu = document.createElement('div');

        Object.assign(contenu.style, {
                backgroundColor: 'white',
                padding: '2rem',
                borderRadius: '8px',
                textAlign: 'center',
                boxShadow: '0 0 15px rgba(0,0,0,0.3)'
            }
        );

        // ---------------------------------------------------------------------
        // Message
        // ---------------------------------------------------------------------

        const message = document.createElement('p');

        message.textContent = 'Veuillez patienter...';

        // ---------------------------------------------------------------------
        // Spinner CSS pur
        // ---------------------------------------------------------------------

        const spinner = document.createElement('div');

        Object.assign(spinner.style, {
                width: '2rem',
                height: '2rem',
                margin: '1rem auto 0',
                border:
                    '0.25rem solid #ddd',
                borderTopColor: '#0d6efd',
                borderRadius: '50%',
                animation: 'interface-spinner 0.75s linear infinite'
            }
        );

        contenu.appendChild(message);
        contenu.appendChild(spinner);
        modalChargement.appendChild(contenu);
        document.body.appendChild(modalChargement);

        // ---------------------------------------------------------------------
        // Ajouter l'animation CSS une seule fois
        // ---------------------------------------------------------------------

        if (!document.getElementById('interface-spinner-style')) {
            const style = document.createElement('style');
            style.id = 'interface-spinner-style';
            style.textContent = `                @keyframes interface-spinner {                    to {                        transform: rotate(360deg);                    }                }            `;
            document.head.appendChild(style);
        }
    }

    // -------------------------------------------------------------------------
    // Afficher
    // -------------------------------------------------------------------------

    modalChargement.style.display = 'flex';
    return modalChargement;
}


/**
 * Ferme la fenêtre « Veuillez patienter ».
 *
 * @param {HTMLElement|null} modalInstance
 * @returns {Promise<void>}
 */
export async function fermerVeuillezPatienter(modalInstance) {
    if (modalInstance) {
        modalInstance.remove();
    }
}


// =============================================================================
// COMPTEUR ANIMÉ
// =============================================================================

/**
 * Affiche un compteur animé.
 *
 * @param {Object} options
 * @param {HTMLElement} options.conteneur
 * @param {number} options.valeurFinale
 * @param {number} [options.duree=1000]
 * @param {function|null} [options.format=null]
 * @param {Object} [options.styles={}]
 */
export function afficherCompteur({conteneur, valeurFinale, duree = 1000, format = null, styles = {}}) {

    // -------------------------------------------------------------------------
    // Sécurité
    // -------------------------------------------------------------------------

    if (!conteneur || !(conteneur instanceof HTMLElement)) {
        throw new Error('Un élément HTML valide doit être fourni.');
    }

    // -------------------------------------------------------------------------
    // Initialisation
    // -------------------------------------------------------------------------

    const debut = performance.now();
    const depart = 0;

    // -------------------------------------------------------------------------
    // Styles
    // -------------------------------------------------------------------------

    Object.assign(conteneur.style, {
            opacity: '0',
            transform: 'scale(1)',
            transition:
                'opacity 0.4s ease-in, ' +
                'transform 0.2s ease-out',
            fontWeight: 'bold',
            fontSize: '30px',
            color: 'black',
            textAlign: 'center',
            width: '150px',

            ...styles
        }
    );

    // -------------------------------------------------------------------------
    // Easing
    // -------------------------------------------------------------------------

    function easing(t) {

        return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
    }

    // -------------------------------------------------------------------------
    // Animation
    // -------------------------------------------------------------------------

    function step(timestamp) {

        const tempsEcoule = timestamp - debut;
        const progression = Math.min(tempsEcoule / duree, 1);
        const facteur = easing(progression);
        const valeurActuelle = depart + (valeurFinale - depart) * facteur;
        const affichage = Math.floor(valeurActuelle);

        // ---------------------------------------------------------------------
        // Affichage
        // ---------------------------------------------------------------------

        conteneur.innerText = format ? format(affichage) : affichage;

        // ---------------------------------------------------------------------
        // Apparition
        // ---------------------------------------------------------------------

        if (progression >= 0.05 && conteneur.style.opacity !== '1') {
            conteneur.style.opacity = '1';
        }

        // ---------------------------------------------------------------------
        // Zoom
        // ---------------------------------------------------------------------

        if (progression < 0.5) {
            const scale = 1 + 0.3 * (1 - Math.abs(0.5 - progression) * 2);
            conteneur.style.transform = `scale(${scale})`;
        } else {
            conteneur.style.transform = 'scale(1)';
        }

        // ---------------------------------------------------------------------
        // Continuer
        // ---------------------------------------------------------------------

        if (progression < 1) {
            requestAnimationFrame(step);
        } else {
            conteneur.innerText = format ? format(valeurFinale) : valeurFinale;
            conteneur.style.transform = 'scale(1)';
        }
    }

    requestAnimationFrame(step);
}