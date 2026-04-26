<?php

namespace DigitalCollective\CDEK\Infrastructure\Http\OAuth;

use DigitalCollective\CDEK\Domain\Exception\OAuth\CDEKOAuthServiceException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CDEKOAuthService
{
    const string GRANT_TYPE = 'client_credentials';
    // TODO: сюда нужно прокинуть секреты через какую-нибудь штучку типо DI
    const string ENDPOINT = '/v2/oauth/token';
    const string METHOD = 'POST';

    public function __construct(
        private readonly string $clientId = '',
        private readonly string $clientSecret = '',
        private readonly string $baseUrl = 'https://api.cdek.ru',

        private readonly HttpClientInterface $httpClient,
    ) {}

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function authorize(): Response
    {
        $query = [
            'request' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => self::GRANT_TYPE,
            ]
        ];

        $response = $this->httpClient->request(
            method: self::METHOD,
            url: self::ENDPOINT . $this->baseUrl,
            options: ['query' => $query]
        );

        if ($response->getStatusCode() !== 200)
            throw CDEKOAuthServiceException::fromResponse($response->toArray(false));

        $data = $response->toArray(false);

        return new Response(
            accessToken: $data['access_token'],
            jti: $data['jti'],
            expiresIn: $data['expires_in'],
        );
    }
}

