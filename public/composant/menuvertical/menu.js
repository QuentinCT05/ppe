"use strict";

/**
 * Injecte la feuille de style CSS du menu vertical dans le document.
 * Cette fonction évite les inclusions redondantes en vérifiant si la CSS est déjà présente.
 */
function injecterStyle() {
    if (!document.getElementById('menuVerticalStyle')) {
        const link = document.createElement('link');
        link.id = 'menuVerticalStyle';
        link.rel = 'stylesheet';
        link.href = '/composant/menuvertical/menu.css'; // Chemin vers la feuille CSS du menu
        document.head.appendChild(link);
    }
}

/**
 * Crée et insère les éléments HTML principaux du menu vertical :
 * - La barre latérale (<nav id="sidebar">)
 * - Le bouton pour afficher le menu lorsqu’il est replié (bouton hamburger)
 * - Le voile d’arrière-plan (overlay) pour la gestion mobile
 */
function injecterMenuHtml() {
    if (!document.getElementById('sidebar')) {
        const sidebar = document.createElement('nav');
        sidebar.className = 'bg-light p-3 vh-100'; // Bootstrap + hauteur 100% viewport
        sidebar.id = 'sidebar';
        document.body.appendChild(sidebar);
    }

    if (!document.getElementById('showMenu')) {
        const showMenuBtn = document.createElement('button');
        showMenuBtn.id = 'showMenu';
        showMenuBtn.className = 'btn btn-sm btn-outline-danger';
        showMenuBtn.style.cssText = 'display: none; position: fixed; top: 10px; left: 10px; width: auto; padding: 5px 10px;';
        showMenuBtn.textContent = '☰'; // icône hamburger unicode
        document.body.appendChild(showMenuBtn);
    }

    if (!document.getElementById('overlay')) {
        const overlay = document.createElement('div');
        overlay.id = 'overlay';
        document.body.appendChild(overlay);
    }
}

/**
 * Génère la structure HTML du menu à partir d’un tableau d’objets lesOptions.
 * Chaque item doit contenir :
 * - label : texte du lien
 * - href : url de destination
 * Le menu contient aussi un bouton permettant de le replier.
 *
 * @param {Array} lesOptions - Liste des options du menu [{label: string, href: string}, ...]
 */
function genererMenu(lesOptions = []) {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar) {
        return;
    }

    // Bouton pour replier le menu (flèche ◁)
    const toggleButton = document.createElement('button');
    toggleButton.className = 'btn btn-sm btn-outline-danger mb-3';
    toggleButton.id = 'toggleMenu';
    toggleButton.textContent = '◁';

    // Création de la liste de navigation
    const ul = document.createElement('ul');
    ul.className = 'nav flex-column';

    // Création des éléments <li> pour chaque option
    for (const item of lesOptions) {
        const li = document.createElement('li');
        li.className = 'nav-item mb-0';

        const a = document.createElement('a');
        a.className = 'nav-link';
        a.href = item.href;
        a.textContent = item.label;

        li.appendChild(a);
        ul.appendChild(li);
    }

    // Remplace le contenu actuel du sidebar par le bouton et la liste
    sidebar.innerHTML = '';
    sidebar.appendChild(toggleButton);
    sidebar.appendChild(ul);

    // Marquer le lien actif selon l'URL actuelle
    const currentPath = window.location.pathname;
    const navLinks = ul.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        if (normalizePath(link.pathname) === normalizePath(currentPath)) {
            link.classList.add('active');
        }
    });
}

// Fonction pour normaliser le chemin en supprimant les barres obliques en début et fin
function normalizePath(path) {
    return path.replace(/^\/+|\/+$/g, '');
}

/**
 * Initialise la logique d’interaction du menu :
 * - Gestion de l’affichage / repli du menu (avec mémorisation dans localStorage)
 * - Adaptation responsive (affichage mobile avec overlay)
 * - Ajustement du contenu principal <main> pour laisser la place au menu
 */
function initSidebar(largeur) {

    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleMenu');
    const showBtn = document.getElementById('showMenu');
    const overlay = document.getElementById('overlay');
    const STORAGE_KEY = 'menuCollapsed';

    // On cible le contenu principal, supposé dans la balise <main>
    const mainContent = document.querySelector('main');

    // Applique dynamiquement la largeur à la sidebar via variable CSS
    sidebar.style.setProperty('--menu-width', largeur + 'px');
    sidebar.style.width = largeur + 'px';

    // Détecte si on est en affichage mobile selon largeur d’écran
    function isMobile() {
        return window.matchMedia('(max-width: 768px)').matches;
    }

    /**
     * Active ou désactive l’état replié du menu.
     * Modifie l’affichage des boutons, overlay et ajuste le style du <main>.
     *
     * @param {boolean} collapsed - true si menu replié, false si visible
     */
    function setCollapsed(collapsed) {
        if (collapsed) {
            sidebar.classList.add('collapsed');
            showBtn.style.display = 'block';
            toggleBtn.setAttribute('aria-expanded', 'false');

            if (isMobile()) {
                overlay.classList.remove('visible');
            } else if (mainContent) {
                mainContent.style.marginLeft = '0';
                mainContent.style.width = '100%';
            }
        } else {
            sidebar.classList.remove('collapsed');
            showBtn.style.display = 'none';
            toggleBtn.setAttribute('aria-expanded', 'true');

            if (isMobile()) {
                overlay.classList.add('visible');
            } else if (mainContent) {
                mainContent.style.marginLeft = largeur + 'px';
                mainContent.style.width = `calc(100% - ${largeur}px)`;
            }
        }

        // Sauvegarde l’état pour persistance
        localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
    }

    // Lecture de l’état sauvegardé au chargement
    const saved = localStorage.getItem(STORAGE_KEY);
    setCollapsed(saved === '1');

    // Évènements pour boutons et overlay
    toggleBtn.addEventListener('click', () => setCollapsed(true));  // replier
    showBtn.addEventListener('click', () => setCollapsed(false));   // déplier
    overlay.addEventListener('click', () => setCollapsed(true));    // clic overlay = replier

    // Sur mobile, fermer le menu automatiquement quand on clique sur un lien
    if (isMobile()) {
        const navLinks = sidebar.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => setCollapsed(true));
        });
    }
}

/**
 * Fonction publique principale à appeler pour initialiser le menu vertical.
 * Elle injecte le CSS, crée la structure HTML, génère les options et initialise l’interaction.
 *
 * @param {Array} lesOptions - Tableau des options menu [{label: string, href: string}, ...]
 * @param {number} [largeur=160] - Largeur du menu en pixels (par défaut 160px)
 */

export function initialiserMenuVertical(lesOptions, largeur = 160) {
    injecterStyle();
    injecterMenuHtml();
    genererMenu(lesOptions);
    initSidebar(largeur); // On passe la largeur ici
}
