// noinspection JSUnusedGlobalSymbols

"use strict";

// Version 2026.1
// Date : 11/08/2026

import { iconeOeil, iconeOeilBarre } from '/composant/icone/icone.js';

function injecterStylesPassword() {
    if (document.getElementById('password-toggle-style')) {
        return;
    }

    const style = document.createElement('style');
    style.id = 'password-toggle-style';
    style.textContent = `
        .password-wrapper {
            position: relative;
            display: block; /* corrige l'empilement */
            width: 100%;    /* adaptatif */
        }

        .password-wrapper input[type="password"],
        .password-wrapper input[type="text"] {
            width: 100%;
            padding-right: 2.5rem;
            box-sizing: border-box;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 0.5rem;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password svg {
            width: 1.2rem;
            height: 1.2rem;
        }

        @media (max-width: 480px) {
            .toggle-password svg {
                width: 1rem;
                height: 1rem;
            }
        }
    `;
    document.head.appendChild(style);
}

export function initPasswordToggles(parent = document) {
    injecterStylesPassword();

    const lesInputs = parent.querySelectorAll('input[type="password"]:not([data-toggle-ready])');

    for (const input of lesInputs) {
        input.setAttribute('data-toggle-ready', 'true');

        const wrapper = document.createElement('div');
        wrapper.classList.add('password-wrapper');

        // Copie la largeur explicite si elle existe
        const style = window.getComputedStyle(input);
        const largeur = input.style.width || style.width;
        if (largeur && largeur !== 'auto') {
            wrapper.style.width = largeur;
            input.style.width = '100%';
        }

        const button = document.createElement('button');
        button.classList.add('toggle-password');
        button.setAttribute('type', 'button');
        button.setAttribute('aria-label', 'Afficher le mot de passe');
        button.setAttribute('aria-pressed', 'false');
        button.setAttribute('tabindex', '0');

        let visible = false;
        button.appendChild(iconeOeil({}));

        const toggleVisibility = () => {
            visible = !visible;
            input.type = visible ? 'text' : 'password';

            const icon = visible ? iconeOeilBarre({}) : iconeOeil({});
            button.innerHTML = '';
            button.appendChild(icon);

            button.setAttribute('aria-label', visible ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
            button.setAttribute('aria-pressed', visible.toString());
        };

        button.addEventListener('click', toggleVisibility);
        button.addEventListener('keydown', event => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                toggleVisibility();
            }
        });

        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        wrapper.appendChild(button);
    }
}
