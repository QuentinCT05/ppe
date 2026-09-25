// noinspection JSUnusedGlobalSymbols

"use strict";

/**
 * Version 2026.1
 * Date : 11/08/2026
 * Récupère les données injectées par la classe Page côté PHP.
 *
 * Les données sont encodées en JSON dans des balises <script> avec leur ID respectif :
 *
 *     <script type="application/json" id="lesCoureurs">
 *         [...]
 *     </script>
 *
 * @param {string} cle - ID de la balise contenant les données (doit correspondre à la clé passée)
 *
 * @returns {any} Les données associées à l'ID
 *
 * @throws {Error} Si la balise ou les données n'existent pas
 */
export function getData(cle) {
    const script = document.getElementById(cle);
    if (!script) {
        throw new Error(`Impossible de trouver les données avec l'ID '${cle}' dans la page.`);
    }
    try {
        return JSON.parse(script.textContent);
    } catch (error) {
        if (error instanceof SyntaxError) {
            throw new Error(`Erreur lors du parsing des données '${cle}' : ${error.message}`);
        }
        throw error;
    }
}
