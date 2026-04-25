<?php

declare(strict_types=1);

namespace DigitalCollective\YandexCaptcha\Domain\Service;

use DigitalCollective\YandexCaptcha\Domain\ValueObject\VerificationResult;
use DigitalCollective\YandexCaptcha\Domain\ValueObject\YandexCaptchaToken;

/**
 * @see DigitalCollective\YandexCaptcha\Infrastructure\Http\YandexCaptchaVerifier
 */
interface CaptchaVerifierInterface
{
    /**
     * @throws DigitalCollective\YandexCaptcha\Domain\Exception\CaptchaVerificationFailed когда сервис недоступен или ответ невалидный
     */
    public function verify(YandexCaptchaToken $token, ?string $clientIp = null): VerificationResult;
}
