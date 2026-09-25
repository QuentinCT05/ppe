<?php
declare(strict_types=1);

namespace ClasseMetier;

class Categorie
{
    private const CATEGORIES = [
        ['id' => 'MI', 'nom' => 'Minime',  'ageMin' => 14, 'ageMax' => 15, 'distance' => '5 km'],
        ['id' => 'CA', 'nom' => 'Cadet',   'ageMin' => 16, 'ageMax' => 17, 'distance' => '15 km'],
        ['id' => 'JU', 'nom' => 'Junior',  'ageMin' => 18, 'ageMax' => 19, 'distance' => '25 km'],
        ['id' => 'ES', 'nom' => 'Espoir',  'ageMin' => 20, 'ageMax' => 22, 'distance' => 'Illimitée'],
        ['id' => 'SE', 'nom' => 'Senior',  'ageMin' => 23, 'ageMax' => 34, 'distance' => 'Illimitée'],
        ['id' => 'M0', 'nom' => 'Master 0','ageMin' => 35, 'ageMax' => 39, 'distance' => 'Illimitée'],
        ['id' => 'M1', 'nom' => 'Master 1','ageMin' => 40, 'ageMax' => 44, 'distance' => 'Illimitée'],
        ['id' => 'M2', 'nom' => 'Master 2','ageMin' => 45, 'ageMax' => 49, 'distance' => 'Illimitée'],
        ['id' => 'M3', 'nom' => 'Master 3','ageMin' => 50, 'ageMax' => 54, 'distance' => 'Illimitée'],
        ['id' => 'M4', 'nom' => 'Master 4','ageMin' => 55, 'ageMax' => 59, 'distance' => 'Illimitée'],
        ['id' => 'M5', 'nom' => 'Master 5','ageMin' => 60, 'ageMax' => 64, 'distance' => 'Illimitée'],
        ['id' => 'M6', 'nom' => 'Master 6','ageMin' => 65, 'ageMax' => 69, 'distance' => 'Illimitée'],
        ['id' => 'M7', 'nom' => 'Master 7','ageMin' => 70, 'ageMax' => 74, 'distance' => 'Illimitée'],
        ['id' => 'M8', 'nom' => 'Master 8','ageMin' => 75, 'ageMax' => 79, 'distance' => 'Illimitée'],
        ['id' => 'M9', 'nom' => 'Master 9','ageMin' => 80, 'ageMax' => 84, 'distance' => 'Illimitée'],
        ['id' => 'M10','nom' => 'Master 10','ageMin' => 85, 'ageMax' => 89, 'distance' => 'Illimitée'],
        ['id' => 'M11','nom' => 'Master 11','ageMin' => 90, 'ageMax' => 99, 'distance' => 'Illimitée']
    ];

    /**
     * Retourne la liste des catégories en y ajoutant la colonne age et la colonne annee
     * @return array
     */
    public static function getAll(): array
    {
        $annee = (int) date('m') >= 9 ? (int) date('Y') + 1 : (int) date('Y');

        $categories = [];

        foreach (self::CATEGORIES as $cat) {
            $cat['age'] = "{$cat['ageMin']} - {$cat['ageMax']}";
            $cat['annee'] = ($annee - $cat['ageMax']) . ' - ' . ($annee - $cat['ageMin']);
            $categories[] = $cat;
        }

        usort($categories, fn($a, $b) => $a['ageMin'] <=> $b['ageMin']);

        return $categories;
    }
}
