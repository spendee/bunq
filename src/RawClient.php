<?php

namespace Link0\Bunq;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Link0\Bunq\Domain\Keypair;
use Link0\Bunq\Domain\Keypair\PublicKey;
use Link0\Bunq\Middleware\DebugMiddleware;
use Link0\Bunq\Middleware\RequestIdMiddleware;
use Link0\Bunq\Middleware\RequestSignatureMiddleware;
use Link0\Bunq\Middleware\ResponseSignatureMiddleware;
use Psr\Http\Message\ResponseInterface;

final class RawClient
{
    /**
     * @var GuzzleClient
     */
    private $guzzle;

    /**
     * @var HandlerStack
     */
    private $handlerStack;

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
        $this->handlerStack = HandlerStack::create();

        $this->addRequestIdMiddleware($sessionToken);
        $this->addRequestSignatureMiddleware($keyPair);
        $this->addServerResponseMiddleware($serverPublicKey);
        $this->addDebugMiddleware($environment);

        $this->guzzle = new GuzzleClient([
            'base_uri' => $environment->endpoint(),
            'handler'  => $this->handlerStack,
            'headers'  => [
                'User-Agent' => 'Link0 Bunq API Client',
            ],
        ]);
    }

    /**
     * @param string $endpoint
     * @param array  $headers
     *
     * @return array
     */
    public function get(string $endpoint, array $headers = []): array
    {
        return $this->processResponse(
            $this->guzzle->request('GET', $endpoint, [
                'headers' => $headers,
            ])
        );
    }

    /**
     * @param string $endpoint
     * @param array  $body
     * @param array  $headers
     *
     * @return array
     */
    public function post(string $endpoint, array $body, array $headers = []): array
    {
        return $this->processResponse(
            $this->guzzle->request('POST', $endpoint, [
                'json' => $body,
                'headers' => $headers,
            ])
        );
    }

    /**
     * @param string $endpoint
     * @param array  $body
     * @param array  $headers
     *
     * @return array
     */
    public function put(string $endpoint, array $body, array $headers = []): array
    {
        return $this->processResponse(
            $this->guzzle->request('PUT', $endpoint, [
                'json' => $body,
                'headers' => $headers,
            ])
        );
    }

    /**
     * @param string $endpoint
     * @param array  $headers
     *
     * @return void
     */
    public function delete(string $endpoint, array $headers = [])
    {
        $this->guzzle->request('DELETE', $endpoint, [
            'headers' => $headers,
        ]);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return mixed
     */
    private function processResponse(ResponseInterface $response)
    {
        $contents = (string) $response->getBody();
        $json = json_decode($contents, true);

        return $json;
    }

    /**
     * @param string $sessionToken
     * @return void
     */
    private function addRequestIdMiddleware(string $sessionToken)
    {
        $this->handlerStack->push(
            Middleware::mapRequest(new RequestIdMiddleware($sessionToken)),
            'bunq_request_id'
        );
    }

    /**
     * @param Keypair $keypair
     * @return void
     */
    private function addRequestSignatureMiddleware(Keypair $keypair)
    {
        // TODO: Figure out if we can skip this middleware on POST /installation
        $this->handlerStack->push(
            Middleware::mapRequest(new RequestSignatureMiddleware($keypair->privateKey())),
            'bunq_request_signature'
        );
    }

    /**
     * @param PublicKey|null $serverPublicKey
     * @return void
     */
    private function addServerResponseMiddleware(PublicKey $serverPublicKey = null)
    {
        // If we have obtained the server's public key, we can verify responses
        if ($serverPublicKey instanceof PublicKey) {
            $this->handlerStack->push(
                Middleware::mapResponse(new ResponseSignatureMiddleware($serverPublicKey))
            );
        }
    }

    /**
     * @param Environment $environment
     * @return void
     */
    private function addDebugMiddleware(Environment $environment)
    {
        if ($environment->inDebugMode()) {
            $this->handlerStack->push(DebugMiddleware::tap(), 'debug_tap');
        }
    }
}
