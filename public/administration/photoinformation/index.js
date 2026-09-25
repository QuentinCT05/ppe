"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from "/composant/fonction/ajax.js";
import {configurerFormulaire, effacerLesErreurs} from "/composant/fonction/formulaire.js";
import {fichierValide, verifierImage,} from "/composant/fonction/fichier.js";
import {creerBoutonSuppression} from "/composant/fonction/dom.js";
import {afficherToast, confirmer} from "/composant/fonction/afficher.js";
import {getData} from "/composant/fonction/page.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

// Répertoire de stockage
const repertoire = '/data/photoinformation';

// les extensions autorisées
const lesExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif'];

// Taille maximale (300 Ko)
const maxSize = 300 * 1024;

// Redimensionnement automatique
const redimensionner = true;

// Dimensions maximales
const width = 350;
const height = 0;


const lesFichiers = getData('lesFichiers');

// récupération des élements sur l'interface
const cible = document.getElementById('cible');
const fichier = document.getElementById('fichier');
const lesCartes = document.getElementById('lesCartes');

// -----------------------------------------------------------------------------------
// Procédures évènementielles
// -----------------------------------------------------------------------------------

// Déclencher le clic sur le champ de type file lors d'un clic dans la zone cible
cible.onclick = () => fichier.click();

// // ajout du glisser déposer dans la zone cible
cible.ondragover = (e) => e.preventDefault();
cible.ondrop = (e) => {
    e.preventDefault();
    controlerFichier(e.dataTransfer.files[0]);
};

// Lancer la fonction controlerFichier si un fichier a été sélectionné dans l'explorateur
fichier.onchange = () => {
    if (fichier.files.length > 0) {
        controlerFichier(fichier.files[0]);
    }
};

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

/**
 * Affichage des fichiers du répertoire
 */
function afficher(data) {
    // On vide le contenu précédent
    lesCartes.innerHTML = '';
    // Création d'un conteneur flex
    let conteneur = document.createElement('div');
    conteneur.style.display = 'flex';
    conteneur.style.flexWrap = 'wrap';
    conteneur.style.gap = '1rem'; // Espacement entre les cartes
    conteneur.style.justifyContent = 'flex-start';
    // Boucle sur les fichiers
    for (const nomFichier of data) {
        const carte = creerCartePhoto(nomFichier);

        // Limite de taille et comportement responsif
        carte.style.flex = '0 1 300px'; // Ne grandit pas, peut rétrécir, base 300px
        carte.style.maxWidth = '100%';

        conteneur.appendChild(carte);
    }
    lesCartes.appendChild(conteneur);
}

/**
 * Crée une carte Bootstrap affichant une photo, avec un bouton de suppression.
 *
 * @param {string} nomFichier - Le nom du fichier image à afficher dans la carte.
 * @returns {HTMLElement} - Élément DOM représentant la carte complète.
 */
function creerCartePhoto(nomFichier) {
    // Création de la carte principale (div avec classe Bootstrap "card")
    const carte = document.createElement('div');
    carte.classList.add("card", "mb-3"); // "mb-3" ajoute une marge inférieure
    carte.id = nomFichier; // Utilisation du nom du fichier comme ID unique

    // Création de l'entête de la carte (section supérieure)
    const entete = document.createElement('div');
    entete.classList.add("card-header");

    // Création du bouton ✘ pour supprimer l'image
    const btnSupprimer = creerBoutonSuppression(() => confirmer(() => supprimer(nomFichier)));
    btnSupprimer.classList.add('float-end'); // Positionné à droite

    // Création d'un élément pour afficher le nom du fichier dans l'entête
    const nom = document.createElement('div');
    nom.classList.add('float-start'); // Positionné à gauche
    nom.innerText = nomFichier;

    // Assemblage de l'entête : bouton et nom
    entete.appendChild(btnSupprimer);
    entete.appendChild(nom);

    // Ajout de l'entête dans la carte
    carte.appendChild(entete);

    // Création du corps de la carte contenant l'image
    const corps = document.createElement('div');
    corps.classList.add("card-body");
    corps.style.height = '250px'; // Hauteur fixe pour uniformiser les cartes

    // Création de l'image à afficher
    const img = document.createElement('img');
    img.src = repertoire + '/' + nomFichier; // Chemin complet de l'image
    img.alt = ""; // Texte alternatif vide pour accessibilité
    img.style.maxWidth = '100%';
    img.style.maxHeight = '100%';
    img.style.width = 'auto';
    img.style.height = 'auto';
    img.style.objectFit = 'contain';

    // Insertion de l'image dans le corps de la carte
    corps.appendChild(img);

    // Ajout du corps dans la carte
    carte.appendChild(corps);

    // Retour de la carte complète prête à être insérée dans le DOM
    return carte;
}

/**
 * Contrôle le fichier sélectionné au niveau de son extension, de sa taille et de ses dimensions
 * Affiche un message d'erreur sous le champ fichier si le fichier n'est pas valide
 * Si le fichier est valide, lance la procédure d'ajout
 * @param file
 */
function controlerFichier(file) {
    // Efface les erreurs précédentes
    effacerLesErreurs();
    // Vérification de taille et d'extension
    if (!fichierValide(file, maxSize, lesExtensions)) {
        return;
    }

    // si le redimensionnement est demandé, on ne vérifie pas les dimensions
    if (redimensionner) {
        ajouter(file);
    } else {
        // sinon on vérifie les dimensions
        verifierImage(file, redimensionner, width, height, () => ajouter(file));
    }
}

/**
 * Ajoute un fichier à la liste des fichiers et l'affiche dans l'interface.
 * @param file
 */
function ajouter(file) {
    let formData = new FormData();
    formData.append('fichier', file);
    appelAjax({
        url: 'ajax/ajouter.php',
        data: formData,
        success: (data) => {
            afficherToast("La photo a été ajoutée dans la bibliothèque");
            // Mise à jour de l'interface
            afficher(data);
        }
    });
}

/**
 * Lance la suppression côté serveur
 * @param {string} nomFichier  nom du fichier à supprimer
 */
function supprimer(nomFichier) {
    effacerLesErreurs();
    appelAjax({
        url: 'ajax/supprimer.php',
        data: {
            nomFichier: nomFichier,
        },
        success: (data) => {
            afficherToast("La photo a été supprimée de la bibliothèque");
            // mise à jour de l'interface
            afficher(data);
        }
    });
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

configurerFormulaire();
afficher(lesFichiers);