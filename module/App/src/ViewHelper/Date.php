<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 03.06.2020
 * Time: 22:21
 */

namespace App\ViewHelper;

use DateTime;
use Laminas\View\Helper\AbstractHelper;

/**
 * Class Date
 * @package App\ViewHelper
 */
class Date extends AbstractHelper
{
    const FORMAT_ADMIN_SIMPLE = 'Y-m-d H:i:s';

    const DEFAULT_DASH = '-';

    /**
     * @param int|string $unixTimestamp
     * @param string $format // A standard PHP date format
     * @param string $default
     * @return string
     */
    public function __invoke($unixTimestamp, string $format, string $default = self::DEFAULT_DASH): string
    {
        $dateTime = new DateTime;
        $dateTime->setTimestamp((int) $unixTimestamp);
        return $dateTime->format($format);
    }
}