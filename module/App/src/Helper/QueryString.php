<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 04.06.2020
 * Time: 00:48
 */

namespace App\Helper;

/**
 * Class
 * @package App\Helper
 */
abstract class QueryString
{
    /**
     * @param string $queryString
     * @param array $paramsRemove
     * @param array $paramsUpsert
     * @return string
     */
    public static function queryStringParams(string $queryString = '', array $paramsRemove = [], array $paramsUpsert = []): string
    {
        $queryParts = explode('?', $queryString);
        $queryPath = $queryParts[0] ?? '';
        $queryParametrical = $queryParts[1] ?? '';

        $exploded = [];
        $explodedParts = $queryParametrical === '' ? [] : explode('&', $queryParametrical); // parse parametrical uri
        foreach ($explodedParts as $explodedPart) {
            $explodedPart = explode('=', $explodedPart);
            if (count($explodedPart) < 1) {
                continue;
            }
            $exploded[$explodedPart[0]] = $explodedPart[1] ?? '';
        }
        unset($explodedParts);
        $exploded = array_map(function ($value) {
            return urldecode($value);
        }, $exploded); // decode parsed uri

        $exploded = array_filter($exploded, function ($paramName) use ($paramsRemove) {
            return !in_array($paramName, $paramsRemove);
        }, ARRAY_FILTER_USE_KEY); // remove page param from the parsed uri

        foreach ($paramsUpsert as $paramName => $paramValue) {
            $exploded[$paramName] = $paramValue;
        }

        $queryParametrical = http_build_query($exploded); // build parametrical uri from parsed uri

        // create the full URI
        $result = $queryPath . ($queryParametrical === '' ? '' : '?' . $queryParametrical);

        return $result;
    }

    /**
     * @param string $orderString
     * @return array
     * @throws \Exception
     */
    public static function parseOrderString(string $orderString): array
    {
        $result = [];

        $orderArray = explode(',', $orderString);

        foreach ($orderArray as $orderItem) {
            $orderItemExploded = explode('_', $orderItem);
            if (count($orderItemExploded) < 2) {
                $message = sprintf('Cannot parse order item: %s', $orderItem);
                throw new \Exception($message);
            }
            $direction = mb_strtolower(array_pop($orderItemExploded));
            if (!in_array($direction, ['asc','desc',])) {
                $message = sprintf('Unknown order direction: %s', $direction);
                throw new \Exception($message);
            }

            $result[implode('_', $orderItemExploded)] = $direction;
        }

        return $result;
    }

    /**
     * @param array $orderArray
     * @return string
     */
    public static function composeOrderString(array $orderArray = []): string
    {
        if (count($orderArray) === 0) {
            return '';
        }

        foreach ($orderArray as $k => $v) {
            $orderArray[$k] = $k . '_' . $v;
        }

        $result = implode(',', $orderArray);

        return $result;
    }
}