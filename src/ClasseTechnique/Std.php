<?php
declare(strict_types=1);

namespace ClasseTechnique;

use DateTimeImmutable;
use HTMLPurifier;
use HTMLPurifier_Config;

/**
 * Classe Std : Classe statique permettant l'affichage, le contrôle et la conversion des données
 * Version 2026.4
 * @Author : Guy Verghote
 * @Date : 22/09/2026
 */
class Std
{
    private static ?HTMLPurifier $purifier = null;

    /**
     * Initialise et retourne l'instance configurée de HTMLPurifier.
     */
    private static function getPurifier(): HTMLPurifier
    {
        if (self::$purifier === null) {
            $config = HTMLPurifier_Config::createDefault();

            // 1. Configuration de base
            $config->set('Core.Encoding', 'UTF-8');
            // Transitional accepte les balises classiques comme <u> sans lever d'erreur
            $config->set('HTML.Doctype', 'XHTML 1.0 Transitional');

            // 2. Gestion du cache
            $cachePath = __DIR__ . '/../cache/htmlpurifier';
            if (!is_dir($cachePath) && !@mkdir($cachePath, 0755, true)) {
                $cachePath = sys_get_temp_dir();
            }
            $config->set('Cache.SerializerPath', $cachePath);

            // 3. Sécurité et gestion des liens/images
            $config->set('HTML.Nofollow', true);
            $config->set('HTML.TargetBlank', true); // Sécurise target="_blank"
            $config->set('Attr.DefaultImageAlt', ''); // Évite de supprimer les <img> sans alt

            self::$purifier = new HTMLPurifier($config);
        }

        return self::$purifier;
    }

    /**
     * Nettoie une chaîne de caractères contenant du HTML.
     */
    public static function nettoyerHtml(?string $valeur): string
    {
        if ($valeur === null || trim($valeur) === '') {
        return '';
    }

        return self::getPurifier()->purify($valeur);
    }

    /**
     * Vérifie l'existence de plusieurs variables dans $_REQUEST
     */
    public static function existe(string ...$champs): bool
    {
        foreach ($champs as$champ) {
            if (!isset($_REQUEST[$champ])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Supprime les espaces superflus
     */
    public static function supprimerEspace(string $valeur): string
    {
        $valeur = preg_replace('/[\s\p{Z}]+/u', ' ',$valeur);
        return trim($valeur ?? '');
    }

    /**
     * Supprime les accents d'une chaîne
     */
    public static function supprimerAccent(string $valeur): string
    {
        if (class_exists('Transliterator')) {
            $trans = \Transliterator::create('Any-Latin; Latin-ASCII');
            if ($trans !== null) {
                return $trans->transliterate($valeur);
            }
        }

        $lesAccents = [
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
            'Ø' => 'O', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ø' => 'o', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'Ý' => 'Y', 'ý' => 'y', 'ÿ' => 'y',
            'Ç' => 'C', 'ç' => 'c', 'Ñ' => 'N', 'ñ' => 'n',
            'Æ' => 'AE', 'æ' => 'ae', 'ß' => 'ss', 'Þ' => 'TH', 'þ' => 'th', 'Ð' => 'DH', 'ð' => 'dh'
        ];
        return strtr($valeur,$lesAccents);
    }

    /**
     * Encode une date jj/mm/aaaa vers aaaa-mm-jj
     */
    public static function encoderDate(string $date): string
    {
        if (!self::dateFrValide($date)) {
            return '';
        }

        $d = DateTimeImmutable::createFromFormat('d/m/Y',$date);
        return $d ? $d->format('Y-m-d') : '';
    }

    /**
     * Decode une date aaaa-mm-jj vers jj/mm/aaaa
     */
    public static function decoderDate(string $date): string
    {
        if (!self::dateMysqlValide($date)) {
            return '';
        }

        $d = DateTimeImmutable::createFromFormat('Y-m-d',$date);
        return $d ? $d->format('d/m/Y') : '';
    }

    /**
     * Vérifie une date française jj/mm/aaaa
     */
    public static function dateFrValide(string $valeur): bool
    {
        if (!preg_match('`^(\d{2})/(\d{2})/(\d{4})$`', $valeur,$m)) {
            return false;
        }
        return checkdate((int)$m[2], (int)$m[1], (int)$m[3]) && (int)$m[3] > 1900;
    }

    /**
     * Vérifie une date MySQL aaaa-mm-jj
     */
    public static function dateMysqlValide(string $valeur): bool
    {
        if (!preg_match('`^(\d{4})-(\d{2})-(\d{2})$`', $valeur,$m)) {
            return false;
        }
        return checkdate((int)$m[2], (int)$m[3], (int)$m[1]) && (int)$m[1] > 1900;
    }

    /**
     * Vérifie la syntaxe d'une URL
     */
    public static function urlValide(string $valeur): bool
    {
        return filter_var($valeur, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Vérifie que le serveur distant répond
     */
    public static function urlAccessible(string $valeur): bool
    {
        if (!self::urlValide($valeur)) {
            return false;
        }

        $ch = curl_init($valeur);
        if ($ch === false) {
            return false;
        }

        curl_setopt_array($ch, [
            CURLOPT_NOBODY => true,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_FOLLOWLOCATION => true
        ]);

        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode >= 200 &&$httpCode < 400;
    }

    /**
     * Vérifie un email
     */
    public static function emailValide(string $valeur): bool
    {
        return filter_var($valeur, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Vérifie que le domaine possède une entrée DNS (MX ou A)
     */
    public static function domaineEmailExiste(string $valeur): bool
    {
        if (!self::emailValide($valeur)) {
            return false;
        }

        $domaine = substr((string)strrchr($valeur, "@"), 1);
        return checkdnsrr($domaine, 'MX') || checkdnsrr($domaine, 'A');
    }

    /**
     * Vérifie la complexité d'un mot de passe (UTF-8)
     */
    public static function passwordValide(string $valeur, int$longueur = 8): bool
    {
        return mb_strlen($valeur, 'UTF-8') >=$longueur
            && preg_match('/[a-z]/', $valeur) === 1             && preg_match('/[A-Z]/',$valeur) === 1
            && preg_match('/\d/', $valeur) === 1             && preg_match('/[\W_]/',$valeur) === 1;
    }

    /**
     * Code postal français
     */
    public static function codePostalValide(string $valeur): bool
    {
        return preg_match('/^(?:0[1-9]|[1-8]\d|9[0-8])\d{3}$/',$valeur) === 1;
    }

    /**
     * Mobile français
     */
    public static function mobileValide(string $valeur): bool
    {
        return preg_match('/^0[67]\d{8}$/',$valeur) === 1;
    }

    /**
     * Fixe français
     */
    public static function fixeValide(string $valeur): bool
    {
        return preg_match('/^0[1-59]\d{8}$/',$valeur) === 1;
    }

    /**
     * Temps hh:mm:ss
     */
    public static function tempsValide(string $valeur): bool
    {
        return preg_match('/^([01]?\d|2[0-3]):[0-5]\d:[0-5]\d$/',$valeur) === 1;
    }

    /**
     * Nom simple
     */
    public static function nomSansAccentValide(string $valeur): bool
    {
        return preg_match("/^[a-z]+([' -]?[a-z]+)*$/i", $valeur) === 1;
    }

    /**
     * Nom avec accents (Regex PCRE corrigée)
     */
    public static function nomAvecAccentValide(string $valeur): bool
    {
        return preg_match("/^[\p{L}\p{M}]+(?:[ '\x{2019}-][\p{L}\p{M}]+)*$/u", $valeur) === 1;
    }

    /**
     * Nettoie un titre
     */
    public static function nettoyerTitre(string $titre): string
    {
        if (class_exists('Normalizer')) {
            $titre = \Normalizer::normalize($titre, \Normalizer::FORM_C) ?:$titre;
        }
        return self::supprimerEspace($titre);
    }

    /**
     * Vérifie la validité d'un titre
     */
    public static function titreValide(string $titre, int$longueurMax = 255): bool
    {
        $titre = self::nettoyerTitre($titre);

        if ($titre === '' || mb_strlen($titre, 'UTF-8') >$longueurMax) {
            return false;
        }

        if (preg_match('/[\p{L}\p{N}]/u', $titre) !== 1) {
            return false;
        }

        if (preg_match('/[\p{Cc}\p{Cf}]/u', $titre) === 1) {
            return false;
        }

        return true;
    }

    /**
     * Entier valide
     */
    public static function nombreEntierValide(int|string $valeur): bool
    {
        return is_int($valeur) || preg_match('/^-?\d+$/', (string)$valeur) === 1;
    }

    /**
     * Réel valide
     */
    public static function nombreReelValide(int|float|string $valeur): bool
    {
        return is_numeric($valeur);
    }

    /**
     * Liste des fichiers d'un dossier filtrée par extensions
     */
    public static function getLesFichiers(string $rep, array $extensions = [], string$order = "a"): array
    {
        if (!is_dir($rep)) {
            return [];
        }

        $liste = [];
        $fichiers = scandir($rep);
        if ($fichiers === false) {
            return [];
        }

        // Normalisation des extensions en minuscules
        $extensions = array_map('strtolower',$extensions);

        foreach ($fichiers as$fichier) {
            if ($fichier === '.' || $fichier === '..' || is_dir($rep . '/' . $fichier)) {
                continue;
            }

            if ($extensions === []) {
                $liste[] =$fichier;
                continue;
            }

            $ext = strtolower(pathinfo($fichier, PATHINFO_EXTENSION));
            if (in_array($ext,$extensions, true)) {
                $liste[] =$fichier;
            }
        }

        natcasesort($liste);
        if (strtolower($order) === 'd') {
            $liste = array_reverse($liste);
        }

        return array_values($liste);
    }
}