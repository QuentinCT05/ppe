"use strict";

// Version 2026.1
// Date version : 30/06/2026

export function getData(id) {
    const element = document.getElementById(id);

    if (!element) {
        throw new Error(`Impossible de trouver '${id}'`);
    }

    return JSON.parse(element.textContent);
}