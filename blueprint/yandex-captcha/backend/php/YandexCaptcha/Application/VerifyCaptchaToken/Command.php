<?php

declare(strict_types=1);

namespace DigitalCollective\YandexCaptcha\Application\VerifyCaptchaToken;

use DigitalCollective\Shared\Interface\Application\ApplicationCommandInterface;
use DigitalCollective\YandexCaptcha\Domain\ValueObject\YandexCaptchaToken;

final readonly class Command implements ApplicationCommandInterface
{
    public function __construct(
        public YandexCaptchaToken $token,
        public ?string $clientIp = null,
    ) {
    }
}
