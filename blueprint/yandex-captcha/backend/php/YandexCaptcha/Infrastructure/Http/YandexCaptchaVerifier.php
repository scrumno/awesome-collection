<?php

declare(strict_types=1);

namespace DigitalCollective\YandexCaptcha\Infrastructure\Http;

use DigitalCollective\YandexCaptcha\Domain\Exception\CaptchaVerificationFailed;
use DigitalCollective\YandexCaptcha\Domain\Service\CaptchaVerifierInterface;
use DigitalCollective\YandexCaptcha\Domain\ValueObject\YandexCaptchaToken;
use DigitalCollective\YandexCaptcha\Infrastructure\Http\Enum\StatusType;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class YandexCaptchaVerifier implements CaptchaVerifierInterface
{
    private const ENDPOINT = 'https://smartcaptcha.yandexcloud.net/validate';

    public function __construct(
        private HttpClientInterface $httpClient,
        private string              $serverKey,
    ) {}

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function verify(YandexCaptchaToken $token, ?string $clientIp = null): Response
    {
        $query = [
            'secret' => $this->serverKey,
            'token' => $token->value,
        ];

        if ($clientIp !== null) $query['ip'] = $clientIp;

        $response = $this->httpClient->request('GET', self::ENDPOINT, [
            'query' => $query,
            'timeout' => 5.0,
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200)
            throw CaptchaVerificationFailed::unexpectedStatus($statusCode);

        /** @var array{status?: string, message?: string, host?: string} $data */
        $data = $response->toArray(throw: false);

        return match ($data['status']) {
            StatusType::OK => new Response(isSuccess: true, message: 'ok'),

            StatusType::Failed => new Response(isSuccess: false, message: $data['message']),

            default => new Response(isSuccess: false),
        };
    }
}
