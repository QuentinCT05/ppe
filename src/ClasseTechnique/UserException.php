<?php declare(strict_types=1);

namespace ClasseTechnique;

use Exception;

/**
 * Exception applicative destinée à être présentée à l'utilisateur.
 *
 * Le code HTTP permet d'associer l'erreur métier
 * au statut HTTP approprié.
 *
 * @Version 2026.3
 * @Date : 24/08/2026
 */
class UserException extends Exception
{
    /**
     * @param string $message Message destiné à l'utilisateur.
     * @param int $codeHttp Code HTTP associé à l'erreur.
     */
    public function __construct(string $message, private readonly int $codeHttp = 400)
    {
        parent::__construct($message);
    }

    /**
     * Retourne le code HTTP associé à l'erreur.
     */
    public function getCodeHttp(): int
    {
        return $this->codeHttp;
    }
}