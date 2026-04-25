<?php

declare(strict_types=1);

namespace DigitalCollective\YandexCaptcha\Domain\ValueObject;

final readonly class VerificationResult
{
    /**
     * @param list<string> $errors
     */
    public function __construct(
        public bool $isSuccess,
        public string $hostname = '',
        public array $errors = [],
    ) {
    }

    public static function success(string $hostname = ''): self
    {
        return new self(true, $hostname);
    }

    /**
     * @param list<string> $errors
     */
    public static function failure(array $errors): self
    {
        return new self(false, '', $errors);
    }
}
