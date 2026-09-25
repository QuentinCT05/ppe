<?php

declare(strict_types=1);

namespace Service;

use ClasseTechnique\Config;
use ClasseTechnique\ImageManager;
use ClasseTechnique\PdfManager;

class FileManagerFactory
{

    public static function classement(): PdfManager
    {
        return self::createPdfManager('classement', DOSSIER_CLASSEMENT);
    }

    public static function information(): ImageManager
    {
        return self::createImageManager('information', DOSSIER_PHOTO_INFORMATION);
    }

    public static function membre(): ImageManager
    {
        return self::createImageManager('membre', DOSSIER_PHOTO_MEMBRE);
    }

    private static function createPdfManager(string $config, string $repertoire): PdfManager
    {
        $parametres = Config::chargerPhp($config);

        return new PdfManager(
            repertoire: $repertoire,
            maxSize: $parametres['maxSize'] ?? 0,
            sansAccent: $parametres['sansAccent'] ?? true
        );
    }

    private static function createImageManager(string $config, string $repertoire): ImageManager
    {
        $parametres = Config::chargerPhp($config);

        return new ImageManager(
            repertoire: $repertoire,
            maxSize: $parametres['maxSize'] ?? 0,
            sansAccent: $parametres['sansAccent'] ?? true,
            redimensionner: $parametres['redimensionner'] ?? false,
            width: $parametres['width'] ?? 0,
            height: $parametres['height'] ?? 0
        );
    }
}