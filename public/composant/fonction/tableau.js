// noinspection JSUnusedGlobalSymbols

"use strict";

// Version 2026.1
// Date version : 27/11/2026

/**
 * Mélange un tableau d'éléments
 * Principe : on prend le dernier élément et on le permute avec un autre élément choisi aléatoirement
 * @param {Array} lesElements
 */
export function melanger(lesElements) {
    for (let i = lesElements.length - 1; i > 0; i -= 1) {
        const j = Math.floor(Math.random() * (i + 1));
        [lesElements[i], lesElements[j]] = [lesElements[j], lesElements[i]];
    }
}

/**
 * Active le tri sur un tableau HTML à partir d'une source dynamique.
 *
 * @param {Object} options
 * @param {string} options.idTable - ID du <table>
 * @param {Function} options.getData - Fonction retournant le tableau de données à trier
 * @param {Function} options.afficher - Fonction pour afficher de nouveau les données dans le tableau
 * @param {Object} [options.triInitial] - Colonne et sens à afficher visuellement sans trier
 */
export function activerTri({idTable, getData, afficher, triInitial = {}}) {
    const table = document.getElementById(idTable);
    if (!table) {
        return;
    }

    const thead = table.querySelector("thead");
    if (!thead || thead.rows.length === 0) {
        return;
    }

    const ligneEntete = thead.rows[0];
    const lesCellules = ligneEntete.cells;

    const tri = {
        colonne: triInitial.colonne || null,
        sens: triInitial.sens || "asc",
    };

    for (const th of lesCellules) {
        const champ = th.dataset.champ;
        const type = th.dataset.type || "text";
        if (!champ) {
            continue;
        }

        th.style.cursor = "pointer";

        th.onclick = () => {
            // Affiche l’effet visuel
            Array.from(lesCellules).forEach(cell => cell.classList.remove("th-flash", "fade-out"));
            th.classList.add("th-flash");
            setTimeout(() => th.classList.add("fade-out"), 50);
            setTimeout(() => th.classList.remove("th-flash", "fade-out"), 400);

            // Basculer sens ou changer colonne
            if (tri.colonne === champ) {
                tri.sens = tri.sens === "asc" ? "desc" : "asc";
            } else {
                tri.colonne = champ;
                tri.sens = "asc";

                // Si déjà trié ascendant, inverser pour éviter ambiguïté
                const dataNonTriee = getData();
                if (estTrie(dataNonTriee, champ, type)) {
                    tri.sens = "desc";
                }
            }

            const data = getData();
            data.sort((a, b) => comparerValeurs(a[champ], b[champ], type, tri.sens));
            afficher(data);
            mettreAJourFleches();
        };
    }

    if (tri.colonne) {
        // Juste afficher la flèche sans trier
        mettreAJourFleches();
    }

    function mettreAJourFleches() {
        for (const th of lesCellules) {
            const champ = th.dataset.champ;
            const base = th.textContent.replace(/[\s▲▼]*$/, "").trim();

            if (champ === tri.colonne) {
                const fleche = tri.sens === "asc" ? " ▲" : " ▼";
                th.innerHTML = `${base}${fleche}`;
            } else {
                th.innerHTML = base;
            }
        }
    }

    function comparerValeurs(valA, valB, type, sens) {
        if (type === "number") {
            valA = parseFloat(valA);
            valB = parseFloat(valB);
            return sens === "asc" ? valA - valB : valB - valA;
        }

        if (type === "date") {
            valA = parseDateFR(valA);
            valB = parseDateFR(valB);
            return sens === "asc" ? valA - valB : valB - valA;
        }

        valA = String(valA);
        valB = String(valB);
        return sens === "asc" ? valA.localeCompare(valB, 'fr', {sensitivity: 'base'}) : valB.localeCompare(valA, 'fr', {sensitivity: 'base'});
    }

    /**
     * Convertit une date au format jj/mm/aaaa en objet Date
     * @param str
     * @returns {Date}
     */
    function parseDateFR(str) {
        const [jour, mois, annee] = String(str).split('/').map(Number);
        return annee * 10000 + mois * 100 + jour;
    }

    function estTrie(data, champ, type) {
        for (let i = 1; i < data.length; i++) {
            let valA = data[i - 1][champ];
            let valB = data[i][champ];
            if (comparerValeurs(valA, valB, type, "asc") > 0) {
                return false;
            }
        }
        return true;
    }
}

// ------------------------------------------------------------
// Fonction pour exporter un tableau au format CSV
// ------------------------------------------------------------

export function exporterCSV(data, nomFichier) {
    if (!data || data.length === 0) {
        console.warn("Aucune donnée à exporter.");
        return;
    }

    // Nettoyage du nom de fichier pour éviter les caractères interdits
    nomFichier = nomFichier.replace(/[<>:"\/\\|?*]+/g, '_');

    // Récupère les clés (en-têtes) à partir du premier objet
    const cles = Object.keys(data[0]);
    const lignes = [];

    // Ligne d'en-tête
    lignes.push(cles.join(';'));

    // Lignes de données
    for (const ligne of data) {
        const valeurs = [];
        // Pour chaque clé, on récupère la valeur correspondante
        for (const cle of cles) {
            let valeur = ligne[cle];
            // Vérifie si la valeur est un nombre, une chaîne ou null/undefined
            if (valeur === undefined || valeur === null) {
                valeur = '';
            } else {
                // Échapper les guillemets
                valeur = String(valeur).replace(/"/g, '""');

                // Protection contre les injections de formules Excel
                if (/^[=+\-@]/.test(valeur)) {
                    valeur = `'${valeur}`;
                }
            }
            // Encadrer les valeurs avec guillemets
            valeurs.push(`"${valeur}"`);
        }
        // Ajout de la ligne au tableau en séparant les valeurs par des points-virgules
        lignes.push(valeurs.join(';'));
    }

    // Ajout du BOM pour Excel afin d'éviter l'erreur d'encodage et d'un retour à la ligne
    const bom = '\uFEFF';
    const contenu = bom + lignes.join('\n');

    // Blob et téléchargement
    // Créer un blob avec le contenu CSV
    const blob = new Blob([contenu], {type: 'text/csv;charset=utf-8;'});
    // Créer un lien temporaire pour le téléchargement
    const lien = document.createElement('a');
    // le lien pointe vers le blob
    lien.href = URL.createObjectURL(blob);
    // le nom du fichier est celui passé en paramètre
    lien.download = `${nomFichier}.csv`;
    // Ajouter le lien au document, déclencher le téléchargement et le supprimer
    document.body.appendChild(lien);
    lien.click();
    document.body.removeChild(lien);

    // Libération de l'URL du blob pour éviter une fuite mémoire
    URL.revokeObjectURL(lien.href);
}

// ------------------------------------------------------------
// Fonction pour exporter un tableau au format JSON
// ------------------------------------------------------------

export function exporterJSON(data, nomFichier) {
    if (!data || data.length === 0) {
        console.warn("Aucune donnée à exporter.");
        return;
    }

    // Nettoyage du nom de fichier pour éviter les caractères interdits
    nomFichier = nomFichier.replace(/[<>:"\/\\|?*]+/g, '_');

    // Convertir les données en chaîne JSON avec une indentation pour une meilleure lisibilité
    const contenu = JSON.stringify(data, null, 2);

    // Créer un blob avec le contenu JSON
    const blob = new Blob([contenu], {type: 'application/json;charset=utf-8;'});

    // Créer un lien temporaire pour le téléchargement
    const lien = document.createElement('a');

    // Le lien pointe vers le blob
    lien.href = URL.createObjectURL(blob);

    // Le nom du fichier est celui passé en paramètre avec l'extension .json
    lien.download = `${nomFichier}.json`;

    // Ajouter le lien au document, déclencher le téléchargement et le supprimer
    document.body.appendChild(lien);
    lien.click();
    document.body.removeChild(lien);

    // Libération de l'URL du blob pour éviter une fuite mémoire
    URL.revokeObjectURL(lien.href);
}