<?php
declare(strict_types=1);

namespace ClasseTechnique;

use Exception;
use InvalidArgumentException;

/**
 * Classe Controle
 *
 * Centralise les contrôles techniques appliqués à une requête HTTP.
 *
 * Les contrôles disponibles sont notamment :
 *   - méthode HTTP (GET, POST, PUT, DELETE, ...)
 *   - jeton CSRF
 *   - structure de la base
 *
 * Chaque contrôle est délégué à la classe spécialisée correspondante.
 *
 * Exemple :
 *
 *      Controle::verifier('post', 'jeton');
 *
 *      Controle::verifier('post', 'jeton', 'structure');
 *
 * Les options sont insensibles à la casse.
 *
 * @Author : Guy Verghote
 * @Version 2026.3
 * @Date : 21/07/2026
 */
final class Controle
{
    /**
     * Empêche l'instanciation.
     */
    private function __construct()
    {
    }

    /**
     * Vérifie la méthode HTTP utilisée.
     *
     * @param string $methodeAttendue
     *      GET, POST, PUT, PATCH, DELETE...
     *
     * @throws Exception
     */
    public static function verifierMethode(string $methodeAttendue): void
    {
        $methodeAttendue = strtoupper($methodeAttendue);

        if ($_SERVER['REQUEST_METHOD'] !== $methodeAttendue) {
            http_response_code(405);

            throw new Exception(
                "Méthode HTTP '$methodeAttendue' requise."
            );
        }
    }

    /**
     * Exécute une ou plusieurs vérifications.
     *
     * Exemple :
     *
     *      Controle::verifier('post');
     *      Controle::verifier('post', 'jeton');
     *      Controle::verifier('post', 'jeton', 'structure');
     *
     * Les options sont insensibles à la casse.
     *
     * @param string ...$options
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public static function verifier(string ...$options): void
    {
        foreach ($options as $option) {

            switch (strtolower($option)) {

                case 'get':
                case 'post':
                case 'put':
                case 'patch':
                case 'delete':
                    self::verifierMethode($option);
                    break;

                case 'jeton':
                    Jeton::verifier();
                    break;

                case 'structure':
                    Structure::verifier();
                    break;

                default:
                    throw new InvalidArgumentException(
                        "Contrôle inconnu : $option"
                    );
            }
        }
    }
}