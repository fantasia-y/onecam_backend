<?php

namespace App\Service\Auth;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use stdClass;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class JWTHelper
{
    private const KEY_URLS = [
        'apple' => 'https://appleid.apple.com/auth/keys'
    ];

    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function decodeJWT(OAuthProvider $provider, string $token): stdClass
    {
        return JWT::decode($token, JWK::parseKeySet($this->fetchKeys($provider)));
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    private function fetchKeys(OAuthProvider $provider): array
    {
        $url = self::KEY_URLS[$provider->value];
        $response = $this->client->request('GET', $url);

        if ($response->getStatusCode() === 200) {
            return json_decode($response->getContent(), true);
        }

        // TODO handle errors correctly
        return [];
    }
}