<?php

declare(strict_types=1);

namespace DigitalCollective\YandexCaptcha\Application\VerifyCaptchaToken;

use DigitalCollective\YandexCaptcha\Domain\Service\CaptchaVerifierInterface;
use DigitalCollective\YandexCaptcha\Infrastructure\Http\Response;

final readonly class Handler
{
    public function __construct(
        private CaptchaVerifierInterface $verifier,
    ) {}

    public function __invoke(Command $command): Response
    {
        return $this->verifier->verify($command->token, $command->clientIp);
    }
}
