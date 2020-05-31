<?php
/**
 * Created by PhpStorm.
 * User: Serhii Solohub
 * Date: 31.05.2020
 * Time: 14:48
 */

namespace App\Service\Pagination;

/**
 * Class Link
 * @package App\Service\Pagination
 */
class Link
{
    /**
     * @var string
     */
    private $href = '';

    /**
     * @var bool
     */
    private $current = false;

    /**
     * @var int
     */
    private $page = 0;

    /**
     * @var string
     */
    private $text = '';

    /**
     * Link constructor.
     * @param string $href
     * @param bool $current
     * @param int $page
     * @param string|null $text
     */
    public function __construct(string $href, bool $current, int $page, string $text = null)
    {
        $this->href = $href;
        $this->current = $current;
        $this->page = $page;
        $this->text = $text ?? (string)$page;
    }

    /**
     * @return string
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * @return bool
     */
    public function isCurrent(): bool
    {
        return $this->current;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }


}