"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {getData} from "/composant/fonction/page.js";
import {afficherCompteur} from "/composant/fonction/afficher.js";

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

const lesMembres = getData("lesMembres");
const lesSaisons = getData("lesSaisons");

const saison = document.getElementById("saison");
const liste = document.getElementById("liste");
const nb = document.getElementById("nb");
const zoneSaison = document.getElementById("zoneSaison");
const nomR = document.getElementById("nomR");

const modeCartes = document.getElementById("modeCartes");
const modeTableau = document.getElementById("modeTableau");
const template = document.getElementById("template-membre");

const repertoire = '/data/photomembre/'; // Répertoire des photos


// -----------------------------------------------------------------------------------
// Événements
// -----------------------------------------------------------------------------------

saison.addEventListener("change", afficher);
nomR.addEventListener("input", afficher);

modeCartes.addEventListener("change", () => {
    localStorage.setItem("annuaireMode", "cartes");
    afficher();
});
modeTableau.addEventListener("change", () => {
    localStorage.setItem("annuaireMode", "tableau");
    afficher();
});


// -----------------------------------------------------------------------------------
// Création d'une carte membre à partir du template
// -----------------------------------------------------------------------------------

/**
 * Crée une carte membre à partir du template HTML.
 *
 * @param {Object} membre - Les informations du membre
 * @returns {HTMLElement} La carte créée
 */
function creerCardMembre(membre) {
    // Clonage du contenu du template
    const carte = template.content.cloneNode(true);

    // Récupération de l'article
    const article = carte.querySelector(".annuaire-membre");

    // -------------------------------------------------------------------------------
    // Nom et prénom
    // -------------------------------------------------------------------------------

    const nom = carte.querySelector(".annuaire-membre__nom");
    nom.textContent = `${membre.nom} ${membre.prenom}`;

    // -------------------------------------------------------------------------------
    // Adresse mail
    // -------------------------------------------------------------------------------

    const mail = carte.querySelector(".annuaire-membre__mail");
    const mailMasque = carte.querySelector(".annuaire-membre__mail-masque");

    if (membre.mail !== "Non communiqué") {
        mail.textContent = `✉ ${membre.mail}`;
        mail.href = `mailto:${membre.mail}`;
        mailMasque.style.display = "none";
    } else {
        mail.style.display = "none";
        mailMasque.style.display = "inline";
    }

    // -------------------------------------------------------------------------------
    // Téléphone
    // -------------------------------------------------------------------------------

    const telephone = carte.querySelector(".annuaire-membre__telephone");

    const telephoneMasque = carte.querySelector(".annuaire-membre__telephone-masque");

    if (membre.telephone !== "Non renseigné") {
        telephone.textContent = `☎ ${membre.telephone}`;
        // Suppression des espaces pour construire le lien téléphonique
        const numeroTelephone = membre.telephone.replace(/\s/g, "");
        telephone.href = `tel:${numeroTelephone}`;
        telephoneMasque.style.display = "none";
    } else {
        telephone.style.display = "none";
        telephoneMasque.style.display = "inline";
    }

    // -------------------------------------------------------------------------------
    // Photo
    // -------------------------------------------------------------------------------
    const photo = carte.querySelector(".annuaire-membre__photo");
    const image = carte.querySelector(".annuaire-membre__image");
    if (membre.photo !== "Non trouvée" && membre.photo !== "Non renseignée") {
        image.src = repertoire + membre.photo + "?v=" + membre.photoVersion;
        image.alt = `Photo de ${membre.prenom} ${membre.nom}`;
    } else {
        // Pas de photo : on applique la classe prévue pour ce cas
        photo.classList.add("annuaire-membre__photo--vide");
        image.remove();
    }
    return article;
}

// -----------------------------------------------------------------------------------
// Affichage des membres
// -----------------------------------------------------------------------------------

function afficher() {
    liste.innerHTML = "";

    const saisonSelectionnee = saison.value;
    const recherche = nomR.value.trim().toLowerCase();

    // Filtrage
    const membresFiltres = lesMembres.filter(membre => {
        if (saisonSelectionnee !== "*" && saisonSelectionnee !== "" && Number(saisonSelectionnee) !== membre.saison) {
            return false;
        }
        if (recherche !== "") {
            const nom = (membre.nom || "").toLowerCase();
            const prenom = (membre.prenom || "").toLowerCase();
            if (!nom.includes(recherche) && !prenom.includes(recherche)) {
                return false;
            }
        }
        return true;
    });

    if (modeTableau.checked) {
        liste.appendChild(creerTableau(membresFiltres));
    } else {
        liste.appendChild(creerGrille(membresFiltres));
    }

    // Compteur

    afficherCompteur({
        conteneur: nb,
        valeurFinale: membresFiltres.length,
        duree: 500,
        styles: {
            color: "white",
            fontSize: "20px",
            textAlign: "right",
            backgroundColor: "#0790e4",
            padding: "5px",
            borderRadius: "8px",
            margin: "10px 0"
        }
    });
}


// Mode cartes : grille de cartes avec photo
function creerGrille(membres) {
    const grille = document.createElement("div");
    grille.classList.add("annuaire-membres__grille");

    for (const membre of membres) {
        grille.appendChild(creerCardMembre(membre));
    }

    return grille;
}


// Affichage en mode tableau : table sans photo
function creerTableau(membres) {
    const table = document.createElement("table");
    table.classList.add("annuaire-tableau");

    // En-tête
    const thead = table.createTHead();
    const trHead = thead.insertRow();
    for (const col of ["Nom et prénom", "Adresse mail", "Téléphone"]) {
        const th = document.createElement("th");
        th.textContent = col;
        trHead.appendChild(th);
    }

    // Corps
    const tbody = table.createTBody();

    for (const membre of membres) {
        const tr = tbody.insertRow();

        // Nom et prénom
        const tdNom = tr.insertCell();
        tdNom.textContent = `${membre.nom} ${membre.prenom}`;

        // Adresse mail
        const tdMail = tr.insertCell();
        if (membre.mail !== "Non communiqué") {
            const a = document.createElement("a");
            a.href = `mailto:${membre.mail}`;
            a.textContent = membre.mail;
            a.className = "annuaire-tableau__lien";
            tdMail.appendChild(a);
        } else {
            const span = document.createElement("span");
            span.className = "annuaire-tableau__vide";
            span.textContent = "Non communiqué";
            tdMail.appendChild(span);
        }

        // Téléphone
        const tdTel = tr.insertCell();
        if (membre.telephone !== "Non renseigné") {
            const a = document.createElement("a");
            a.href = `tel:${membre.telephone.replace(/\s/g, "")}`;
            a.textContent = membre.telephone;
            a.className = "annuaire-tableau__lien";
            tdTel.appendChild(a);
        } else {
            const span = document.createElement("span");
            span.className = "annuaire-tableau__vide";
            span.textContent = "Non renseigné";
            tdTel.appendChild(span);
        }
    }

    return table;
}



// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

// Initialisation de la liste des saisons

if (lesSaisons.length === 2) {
    zoneSaison.style.visibility = "visible";
    // Option permettant d'afficher les deux saisons
    const libelle = lesSaisons[0].saison + " et " + lesSaisons[1].saison;
    saison.add(new Option(libelle, "*"));
    // Ajout des saisons
    for (const element of lesSaisons) {
        saison.add(new Option(element.saison, element.saison));
    }
}

// Mode d'affichage (cartes / tableau) — restauration de la préférence
const modeInitial = localStorage.getItem("annuaireMode") || "cartes";
if (modeInitial === "tableau") {
    modeTableau.checked = true;
} else {
    modeCartes.checked = true;
}

afficher();