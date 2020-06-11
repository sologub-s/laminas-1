<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 29.05.2020
 * Time: 02:19
 */

namespace App\Validator;

use Laminas\Validator\AbstractValidator;
use App\Helper\Slug as SlugHelper;

/**
 * Class Slug
 * @package App\Validator
 */
class Slug extends AbstractValidator
{
    const SYMBOL_VIOLATION = 'float';

    /**
     * @var string[]
     */
    protected $messageTemplates = [
        self::SYMBOL_VIOLATION => "'%value%' must contain only the URL-safe symbols.",
    ];

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $this->setValue($value);

        $splittedValue = str_split($value);

        if (count(array_diff($splittedValue, array_merge(SlugHelper::DICTIONARIES['ru'], str_split('0123456789_-c')))) > 0) {
            $this->error(self::SYMBOL_VIOLATION);
            return false;
        }

        return true;
    }
}