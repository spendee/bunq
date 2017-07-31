<?php

namespace Link0\Bunq;

use Link0\Bunq\Domain\Keypair;
use Link0\Bunq\Domain\Keypair\PublicKey;
use Link0\Bunq\Domain\ListResponse;

final class Client
{
    /**
     * @var RawClient
     */
    private $rawClient;

    /**
     * @var ResponseMapper
     */
    private $responseMapper;

    /**
     * @param Environment    $environment
     * @param Keypair        $keyPair
     * @param PublicKey|null $serverPublicKey
     * @param string         $sessionToken
     */
    public function __construct(
        Environment $environment,
        Keypair $keyPair,
        PublicKey $serverPublicKey = null,
        string $sessionToken = ''
    )
    {
        $this->rawClient      = new RawClient($environment, $keyPair, $serverPublicKey, $sessionToken);
        $this->responseMapper = new ResponseMapper();
    }

    /**
     * @param string $endpoint
     * @param array  $headers
     *
     * @return array
     */
    public function get(string $endpoint, array $headers = []): array
    {
        return $this->processResponse($this->rawClient->get($endpoint, $headers));
    }

    /**
     * @param string $endpoint
     * @param array  $headers
     *
     * @return ListResponse
     */
    public function list(string $endpoint, array $headers = []): ListResponse
    {
        return new ListResponse($this->rawClient, $this->responseMapper, $endpoint, $headers);
    }

    /**
     * @param string $endpoint
     * @param array $body
     * @param array $headers
     * @return array
     */
    public function post(string $endpoint, array $body, array $headers = []): array
    {
        return $this->processResponse($this->rawClient->post($endpoint, $body, $headers));
    }

    /**
     * @param string $endpoint
     * @param array $body
     * @param array $headers
     * @return array
     */
    public function put(string $endpoint, array $body, array $headers = []): array
    {
        return $this->processResponse($this->rawClient->put($endpoint, $body, $headers));
    }

    /**
     * @param string $endpoint
     * @param array $headers
     * @return void
     */
    public function delete(string $endpoint, array $headers = [])
    {
        $this->rawClient->delete($endpoint, $headers);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function processResponse(array $data): array
    {
        return iterator_to_array(
            $this->responseMapper->mapResponse($data['Response'] ?? [])
        );
    }
}
