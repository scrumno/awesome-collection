<?php

declare(strict_types=1);

namespace DigitalCollective\YandexCaptcha\Infrastructure\Http;

use DigitalCollective\YandexCaptcha\Domain\Exception\CaptchaVerificationFailed;
use DigitalCollective\YandexCaptcha\Domain\Service\CaptchaVerifierInterface;
use DigitalCollective\YandexCaptcha\Domain\ValueObject\VerificationResult;
use DigitalCollective\YandexCaptcha\Domain\ValueObject\YandexCaptchaToken;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class YandexCaptchaVerifier implements CaptchaVerifierInterface
{
    private const ENDPOINT = 'https://smartcaptcha.yandexcloud.net/validate';

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $serverKey,
    ) {}

    public function verify(YandexCaptchaToken $token, ?string $clientIp = null): VerificationResult
    {
        $query = [
            'secret' => $this->serverKey,
            'token' => $token->value,
        ];

        if ($clientIp !== null) $query['ip'] = $clientIp;

        try {
            $response = $this->httpClient->request('GET', self::ENDPOINT, [
                'query' => $query,
                'timeout' => 5.0,
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200)
                throw CaptchaVerificationFailed::unexpectedStatus($statusCode);

            /** @var array{status?: string, message?: string, host?: string} $data */
            $data = $response->toArray(throw: false);
        } catch (HttpClientExceptionInterface $e) {
            throw CaptchaVerificationFailed::transportError($e);
        }

        if ($data['status'] === 'ok')
            return VerificationResult::success(($data['host'] ?? ''));

        return VerificationResult::failure([($data['message'] ?? 'unknown')]);
    }
}
