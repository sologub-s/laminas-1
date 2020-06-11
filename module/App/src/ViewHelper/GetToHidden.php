<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 04.06.2020
 * Time: 00:36
 */

namespace App\ViewHelper;

use DateTime;
use Exception;
use Laminas\View\Helper\AbstractHelper;
use App\Helper\QueryString as QueryStringHelper;

/**
 * Class GetToHidden
 * @package App\ViewHelper
 */
class GetToHidden extends AbstractHelper
{

    /**
     * @param string $parametricalString
     * @param string $imploder
     * @param string $partial
     * @return string
     */
    public function __invoke(string $parametricalString, string $imploder = PHP_EOL, string $partial = 'backend/partial/getToHiddenItem'): string
    {
        $parametricalString = trim($parametricalString, '?');

        $result = [];

        foreach (explode('&', $parametricalString) as $param) {
            $exploded = explode('=', $param);
            $paramName = $exploded[0] ?? '';
            $paramValue = $exploded[1] ?? '';
            $result[] = $this->getView()->partial($partial, [
                'name' => urldecode($paramName),
                'value' => urldecode($paramValue),
            ]);
        }

        return $imploder . implode($imploder, $result) . $imploder;
    }
}