<?php

declare(strict_types=1);

namespace DigitalCollective\YandexCaptcha\Domain\Service;

use DigitalCollective\YandexCaptcha\Domain\ValueObject\YandexCaptchaToken;
use DigitalCollective\YandexCaptcha\Infrastructure\Http\Response;

interface CaptchaVerifierInterface
{
    public function verify(YandexCaptchaToken $token, ?string $clientIp = null): Response;
}
