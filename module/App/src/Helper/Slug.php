<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 28.05.2020
 * Time: 19:06
 */

namespace App\Helper;

/**
 * Class
 * @package App\Helper
 */
abstract class Slug
{
    const DICTIONARIES = [
        'en' => [
            ' ' => '-', '/' => '-',
        ],
        'ru' => [
            'а' => 'a', 'б' => 'b',
            'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ж' => 'j',
            'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l',
            'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h',
            'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => 'y',
            'ы' => 'yi', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            ' ' => '-', '/' => '-'
        ],
        'ua' => [
            'а' => 'a', 'б' => 'b',
            'в' => 'v', 'г' => 'g', 'ґ' => 'g', 'д' => 'd', 'е' => 'e', 'є' => 'e', 'ж' => 'j',
            'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l',
            'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h',
            'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ї' => 'yi',
            'і' => 'i', 'ь' => '', 'ю' => 'yu', 'я' => 'ya',
            ' ' => '-', '/' => '-',
        ],
    ];

    public static function createSlug(string $string = '', $dictionary = 'en'): string
    {
        $tr = self::DICTIONARIES[$dictionary];
        $string = mb_strtolower($string);
        $string = strtr($string, $tr);

        if (preg_match('/[^A-Za-z0-9_\-]/', $string)) {
            $string = preg_replace('/[^A-Za-z0-9_\-]/', '', $string);
        }

        while (strstr($string, '--') !== false) {
            $string = str_replace('--', '-', $string);
        }

        return $string;
    }
}