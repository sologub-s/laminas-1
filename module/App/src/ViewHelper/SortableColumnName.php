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
 * Class SortableColumnName
 * @package App\ViewHelper
 */
class SortableColumnName extends AbstractHelper
{
    const DEFAULT_NONE = 'none';
    const DEFAULT_ASC = 'up';
    const DEFAULT_DESC = 'down';

    /**
     * @param string $queryString
     * @param string $currentOrderString
     * @param string $columnName
     * @param string|null $fieldName
     * @param string|null $defaultOrderString
     * @param string $partial
     * @return string
     * @throws Exception
     */
    public function __invoke(string $queryString, string $currentOrderString, string $columnName, string $fieldName = null, string $defaultOrderString = null, string $partial = 'backend/partial/sortableColumnName'): string
    {
        $isDefault = false;

        $fieldName = $fieldName ?? $columnName;

        $currentOrderArray = QueryStringHelper::parseOrderString($currentOrderString);

        // single column sorting only
        $currentOrderArray = array_filter($currentOrderArray, function ($key) use ($fieldName) {
            return $key === $fieldName;
        }, ARRAY_FILTER_USE_KEY);

        // if column filter is absent then add it with 'asc' ; set current icon to DEFAULT_NONE
        if (!array_key_exists($fieldName, $currentOrderArray)) {
            $currentOrderArray[$fieldName] = 'asc';
            $icon = self::DEFAULT_NONE;
        }
        // elseif column filter is 'asc' then make it desc ; set current icon to DEFAULT_ASC
        elseif ($currentOrderArray[$fieldName] === 'asc') {
            $currentOrderArray[$fieldName] = 'desc';
            $icon = self::DEFAULT_ASC;
        }
        // elseif column filter is 'desc' then remove it ; set current icon to DEFAULT_DESC
        elseif ($currentOrderArray[$fieldName] === 'desc') {
            unset($currentOrderArray[$fieldName]);
            $defaultOrderArray = QueryStringHelper::parseOrderString($defaultOrderString);
            if (array_key_exists($fieldName, $defaultOrderArray) && $defaultOrderArray[$fieldName] === 'desc') {
                $currentOrderArray[$fieldName] = 'asc';
                $isDefault = true;
            }
            $icon = self::DEFAULT_DESC;
        }
        // else throw Exception
        else {
            $message = sprintf('Unknown sorting order: %s', $currentOrderArray[$fieldName]);
            throw new Exception($message);
        }

        // if $currentOrderArray is empty then remove orderBy param
        if (count($currentOrderArray) === 0) {
            $href = QueryStringHelper::queryStringParams($queryString, ['orderBy',]);
        }
        // else upsert orderBy param by the imploded $currentOrderArray
        else {
            $orderBy = QueryStringHelper::composeOrderString($currentOrderArray);
            $href = QueryStringHelper::queryStringParams($queryString, [], ['orderBy' => $orderBy,]);
        }

        // return render partial
        return $this->getView()->partial($partial, [
            'href' => $href,
            'columnName' => $columnName,
            'icon' => $icon,
            'isDefault' => $isDefault,
        ]);
    }
}