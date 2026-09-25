"use strict";

function injecterStyle() {
    if (!document.getElementById('menuHorizontalStyle')) {
        const link = document.createElement('link');
        link.id = 'menuHorizontalStyle';
        link.rel = 'stylesheet';
        link.href = '/composant/menuhorizontal/menu.css';
        document.head.appendChild(link);
    }
}

function genererMenu(donnees) {
    let lesOptions = [];
    let conteneurSelecteur = 'main'; // Valeur par défaut

    // Rétrocompatibilité : gestion Tableau vs Objet
    if (Array.isArray(donnees)) {
        lesOptions = donnees;
    } else if (donnees && typeof donnees === 'object') {
        lesOptions = donnees.options || [];
        if (donnees.conteneur) {
            conteneurSelecteur = donnees.conteneur;
        }
    }

    const conteneur = typeof conteneurSelecteur === 'string' ? document.querySelector(conteneurSelecteur) : conteneurSelecteur;

    if (!conteneur) {
        return;
    }

    const nav = document.createElement('nav');
    nav.id = 'menuHorizontal';

    const ul = document.createElement('ul');

    for (const option of lesOptions) {
        const li = document.createElement('li');
        const a = document.createElement('a');
        a.href = option.href;
        a.textContent = option.label;
        if (typeof option.title === 'string' && option.title.trim() !== '') {
            a.title = option.title.trim();
        }

        const currentPath = location.pathname.replace(/^\/|\/$/g, '');
        const optionPath = option.href.replace(/^\/|\/$/g, '');

        if (currentPath === optionPath) {
            li.classList.add('actif');
        }

        li.appendChild(a);
        ul.appendChild(li);
    }

    nav.appendChild(ul);
    conteneur.insertBefore(nav, conteneur.firstChild);
}

export function initialiserMenuHorizontal(donnees) {
    injecterStyle();
    genererMenu(donnees);
}