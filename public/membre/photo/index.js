"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {getData} from "/composant/fonction/page.js";
import {appelAjax} from "/composant/fonction/ajax.js";
import {afficherToast,} from "/composant/fonction/afficher.js";
import {configurerFormulaire, effacerLesErreurs} from "/composant/fonction/formulaire.js";
import {fichierValide, verifierImage} from "/composant/fonction/fichier.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------


const photo = getData("photo");

const msg = document.getElementById('msg');
const cible = document.getElementById('cible');
const fichier = document.getElementById('fichier');
const btnSupprimer = document.getElementById('btnSupprimer');


// Répertoire de stockage
const repertoire = '/data/photomembre';

// les extensions autorisées
const lesExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif'];

// Taille maximale (200 Ko)
const maxSize = 200 * 1024;

// Redimensionnement automatique
const redimensionner = true;

// Dimensions maximales
const width = 200;
const height = 200;

// -----------------------------------------------------------------------------------
// procédures évènementielles
// -----------------------------------------------------------------------------------

/// Déclencher le clic sur le champ de type file lors d'un clic dans la zone cible
cible.onclick = () => fichier.click();

// // ajout du glisser déposer dans la zone cible
cible.ondragover = (e) => e.preventDefault();
cible.ondrop = (e) => {
    e.preventDefault();
    controlerFichier(e.dataTransfer.files[0]);
};

// traitement du champ file associé aux modifications de photos
fichier.onchange = function () {
    if (this.files.length > 0) {
        controlerFichier(this.files[0]);
    }
};

// suppression de la photo
btnSupprimer.onclick = supprimerPhoto;

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

/**
 * Contrôle le fichier sélectionné au niveau de son extension et de sa taille
 * Vérifie que le fichier est bien une image et que ses dimensions sont correctes si le redimensionnement n'est pas demandé
 * lancer la demande de remplacement de l'image
 * @param file {object} fichier à ajouter
 */
function controlerFichier(file) {
    // Efface les erreurs précédentes
    effacerLesErreurs();
    // Vérification de taille et d'extension
    if (!fichierValide(file, maxSize, lesExtensions)) {
        return;
    }
    // Vérifications spécifiques pour un fichier image
    // La fonction de rappel reçoit implicitement en paramètre l'objet file et l'objet Image créé
    verifierImage(file, redimensionner, width, height, majPhoto);
}

/**
 * Remplace le fichier sélectionné par le nouveau fichier téléversé
 * @param file
 * @param img
 */
function majPhoto(file, img) {
    // Vider la zone de message utilisateur
    msg.innerHTML = "";

    // Créer un objet FormData pour envoyer les données du formulaire
    const formData = new FormData();
    formData.append('fichier', file);

    appelAjax({
        url: 'ajax/remplacermaphoto.php',
        data: formData,
        success: () => {
            // il faut afficher le bouton pour supprimer la photo
            btnSupprimer.style.display = 'block';
            cible.innerHTML = '';
            cible.appendChild(img);
            afficherToast('La photo a été modifiée');
        }
    });
}

/**
 * Supprime la photo
 */
function supprimerPhoto() {
    // Vider la zone de message utilisateur
    msg.innerHTML = "";

    appelAjax({
        url: 'ajax/supprimermaphoto.php',
        success: () => initialiserUpload()
    });
}


function initialiserUpload() {
    // Pas encore de photo ou plus de photo : on affiche l'aide.
    cible.innerHTML = '';
    const icone = document.createElement('span');
    icone.className = 'photo-upload-icon';
    icone.textContent = '📷';
    const texte = document.createElement('span');
    texte.className = 'photo-upload-text';
    texte.innerHTML = 'Déposer une photo<br>ou cliquer ici';
    cible.append(icone, texte);
    btnSupprimer.style.display = 'none';
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

configurerFormulaire();

// alimentation de la zone cible avec la photo si elle existe
if (photo.present) {
    let img = document.createElement('img');
    img.src = repertoire + '/' + photo.photo;
    img.alt = 'photo du membre';
    cible.appendChild(img);
    btnSupprimer.style.display = 'block';
} else {
    initialiserUpload();
}