<?php

declare(strict_types=1);

namespace DigitalCollective\YandexCaptcha\Application\VerifyCaptchaToken;

use DigitalCollective\YandexCaptcha\Domain\Service\CaptchaVerifierInterface;
use DigitalCollective\YandexCaptcha\Domain\ValueObject\VerificationResult;

final readonly class Handler
{
    public function __construct(
        private CaptchaVerifierInterface $verifier,
    ) {}

    public function __invoke(Command $command): VerificationResult
    {
        return $this->verifier->verify($command->token, $command->clientIp);
    }
}
