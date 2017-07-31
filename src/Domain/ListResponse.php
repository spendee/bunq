<?php
declare(strict_types=1);

namespace Link0\Bunq\Domain;

use Link0\Bunq\RawClient;
use Link0\Bunq\ResponseMapper;

final class ListResponse implements \IteratorAggregate
{
    /**
     * @var RawClient
     */
    private $client;

    /**
     * @var ResponseMapper
     */
    private $responseMapper;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var string|null
     */
    private $futureUrl;

    /**
     * @param RawClient      $client
     * @param ResponseMapper $responseMapper
     * @param string         $baseUrl
     * @param array          $headers
     *
     * @internal param string $endpoint
     */
    public function __construct(RawClient $client, ResponseMapper $responseMapper, string $baseUrl, array $headers = [])
    {
        $this->client         = $client;
        $this->responseMapper = $responseMapper;
        $this->baseUrl        = $baseUrl;
        $this->headers        = $headers;
    }

    /**
     * @return null|string
     */
    public function getFutureUrl()
    {
        return $this->futureUrl;
    }

    /**
     * @return \Generator
     */
    public function getIterator()
    {
        $fetchUrl  = $this->baseUrl;
        $keyOffset = 0;

        while ($fetchUrl !== null ) {
            $response = $this->client->get($fetchUrl, $this->headers);

            foreach ($this->responseMapper->mapResponse($response['Response'] ?? []) as $key => $item) {
                yield $keyOffset => $item;
                $keyOffset += 1;
            }

            $this->futureUrl = $response['Pagination']['future_url'] ?? null;
            $fetchUrl        = $response['Pagination']['older_url'] ?? null;
        }
    }
}
