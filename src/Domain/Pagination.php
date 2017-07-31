<?php
declare(strict_types=1);

namespace Link0\Bunq\Domain;

final class Pagination
{
    /**
     * @var string|null
     */
    private $futureUrl;

    /**
     * @var string|null
     */
    private $newerUrl;

    /**
     * @var string|null
     */
    private $olderUrl;

    /**
     * @param array $value
     *
     * @return Pagination
     */
    public static function fromArray(array $value): Pagination
    {
        $pagination = new self();
        $pagination->futureUrl = $value['future_url'] ?? null;
        $pagination->newerUrl  = $value['newer_url']  ?? null;
        $pagination->olderUrl  = $value['older_url']  ?? null;

        return $pagination;
    }
}
