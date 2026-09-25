<?php

declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Génère une page système intégrée dans la charte graphique du site.
 *
 * Cette classe est indépendante de bootstrap.php et d'InterfaceHtml
 * afin de pouvoir être utilisée notamment lors de la gestion des erreurs.
 *
 * @author Guy Verghote
 * @version 2026.1
 * @date 29/08/2026
 */
class InterfaceSystem
{
    /** * Types de message reconnus, associés à leur libellé d'en-tête. */
    private const TYPES = ['erreur' => 'Erreur', 'avertissement' => 'Avertissement', 'information' => 'Information', 'maintenance' => 'Maintenance',];

    private string $message;
    private string $titre;
    private string $type;

    /**
     * @param string $message Message à afficher.
     * @param string $titre Titre de la page.
     */
    public function __construct(string $titre, string $message, string $type = 'avertissement')
    {
        $this->message = $message;
        $this->titre = $titre;
        $this->type = array_key_exists($type, self::TYPES) ? $type : 'avertissement';
    }

    /**
     * Affiche la page système.
     */
    public function afficher(): void
    {
        $message = $this->message;
        $titre = $this->titre;
        $type = $this->type;
        $template = dirname(__DIR__, 2) . '/view/interface_system.php';

        require $template;
        exit;
    }
}
