<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 31.05.2020
 * Time: 12:23
 */

namespace App\Service;

use App\Service\Pagination\Link;
use Exception;

/**
 * Class Pagination
 * @package App\Service
 */
class Pagination
{
    const QUERY_STRING_MODE_PARAM = 1;
    const QUERY_STRING_MODE_PATH = 2;

    /**
     * @var int
     */
    private $perPage = 0;

    /**
     * @var int|null
     */
    private $count = 0;

    /**
     * @var int
     */
    private $curPage = 0;

    /**
     * @var int
     */
    private $radius = 0;

    /**
     * @var int
     */
    private $totalPages = 0;

    /**
     * @var Link[]
     */
    private $links = [];

    /**
     * @var Link|null
     */
    private $previousLink = null;

    /**
     * @var Link|null
     */
    private $nextLink = null;

    /**
     * @var string
     */
    private $previousLinkText = 'Prev';

    /**
     * @var string
     */
    private $nextLinkText = 'Next';

    /**
     * @var string
     */
    private $queryString = '';

    /**
     * @var int
     */
    private $queryStringMode = self::QUERY_STRING_MODE_PATH;

    /**
     * @var string
     */
    private $paramName = 'page';

    /**
     * @var string
     */
    private $pathName = '';

    /**
     * @var int|null
     */
    private $range = null;

    /**
     * @var string
     */
    private $rangeText = '...';

    /**
     * @var bool
     */
    private $paramInTheFirstLink = false;

    /**
     * Pagination constructor.
     * @param int $perPage
     * @param int|null $count
     * @param int $curPage
     * @param int $range
     * @param string $rangeText
     */
    public function __construct(int $perPage = 10, ?int $count = null, int $curPage = 1, ?int $range = null, string $rangeText = '...')
    {
        $this->perPage = $perPage;
        $this->count = $count;
        $this->curPage = $curPage;
        $this->range = $range;
        $this->rangeText = $rangeText;

        return $this;
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @return int
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * @return bool
     */
    public function isFirstPage(): bool
    {
        return $this->curPage === 1;
    }

    /**
     * @return bool
     */
    public function isLastPage(): bool
    {
        return $this->curPage === $this->totalPages || $this->totalPages === 0;
    }

    /**
     * @return Link|null
     */
    public function getPreviousLink(): ?Link
    {
        return $this->previousLink;
    }

    /**
     * @return Link|null
     */
    public function getNextLink(): ?Link
    {
        return $this->nextLink;
    }

    /**
     * @param string $queryString
     * @return self
     */
    public function setQueryString(string $queryString): self
    {
        $this->queryString = $queryString;
        return $this;
    }

    /**
     * @param int $queryStringMode
     * @return self
     */
    public function setQueryStringMode(int $queryStringMode): self
    {
        if (!in_array($queryStringMode, [
            self::QUERY_STRING_MODE_PARAM,
            self::QUERY_STRING_MODE_PATH,
        ])) {
            $message = sprintf('Unknown query string mode: %s', $queryStringMode);
            throw new Exception($message);
        }
        $this->queryStringMode = $queryStringMode;
        return $this;
    }

    public function calculate()
    {
        $this->totalPages = (int)ceil($this->count / $this->perPage);

        $this->curPage = min($this->totalPages, $this->curPage);
        $this->curPage = max(1, $this->curPage);

        for ($i = 1; $i <= $this->totalPages; $i++) {

            // base link item
            $link = [
                'href' => '',
                'current' => false,
                'page' => 0,
            ];

            $link['page'] = $i;
            $link['current'] = $i === $this->curPage;

            $queryParts = explode('?', $this->queryString);
            $queryPath = $queryParts[0] ?? '';
            $queryParametrical = $queryParts[1] ?? '';

            if ($this->queryStringMode === self::QUERY_STRING_MODE_PARAM) {
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

                $exploded = array_filter($exploded, function ($paramName) {
                    return $paramName !== $this->paramName;
                }, ARRAY_FILTER_USE_KEY); // remove page param from the parsed uri

                $exploded[$this->paramName] = $i; // set the page param to desired $i value in the parsed uri

                if ($i === 1 && !$this->paramInTheFirstLink) {
                    unset($exploded[$this->paramName]);
                }
                $queryParametrical = http_build_query($exploded); // build parametrical uri from parsed uri
            }

            if ($this->queryStringMode === self::QUERY_STRING_MODE_PATH) {
                throw new Exception('Not implemented !');
            }

            // create the full href
            $resultHref = $queryPath . ($queryParametrical === '' ? '' : '?' . $queryParametrical);

            $link['href'] = $resultHref;

            $this->links[$i] = new Link($link['href'], $link['current'], $link['page']);
        }

        $previousLinkHref = '#';
        $previousLinkPage = 1;
        if (!$this->isFirstPage()) {
            $previousLink = $this->links[$this->curPage - 1];
            $previousLinkHref = $previousLink->getHref();
            $previousLinkPage = $previousLink->getPage();
        }
        $this->previousLink = new Link($previousLinkHref, false, $previousLinkPage, $this->previousLinkText);

        $nextLinkHref = '#';
        $nextLinkPage = $this->curPage;
        if (!$this->isLastPage()) {
            $nextLink = $this->links[$this->curPage + 1];
            $nextLinkHref = $nextLink->getHref();
            $nextLinkPage = $nextLink->getPage();
        }
        $this->nextLink = new Link($nextLinkHref, false, $nextLinkPage, $this->nextLinkText);

        // range
        $toRemove = [];
        for ($i = 1; $i <= $this->totalPages; $i++) {
            if (is_null($this->range)) {
                continue; // all the links, no ranging
            }
            if (
                (
                    ($i <= $this->curPage && $i > min(1 + $this->range, $this->totalPages)) // rightward of the first page
                    &&
                    ($i <= $this->curPage && $i < max($this->curPage - $this->range, 1)) // leftward of the current page
                )
                ||
                (
                    ($i >= $this->curPage && $i > min($this->curPage + $this->range, $this->totalPages)) // rightward of the current page
                    &&
                    ($i >= $this->curPage && $i < max($this->totalPages - $this->range, 1)) // leftward of the last page
                )
            ) {
                $toRemove[] = $i;
            }
        }

        $skipRemoves = [];
        foreach ($toRemove as $k => $i) {
            if (
                (isset($this->links[$i - 1]) && !in_array($i - 1, $toRemove))
                &&
                (isset($this->links[$k + 1]) && !in_array($i + 1, $toRemove))
            ) {
                $skipRemoves[] = $k; // do not remove links if we have to remove only one link per line
            }
        }

        foreach ($toRemove as $k => $i) {
            if (in_array($k, $skipRemoves)) {
                continue;
            }
            if (isset($toRemove[$k + 1]) && $toRemove[$k + 1] === $i + 1) { // insert only one range element at line
                unset($this->links[$i]);
                continue;
            }
            $this->links[$i] = new Link('#', false, 0, $this->rangeText, true);
        }
    }
}