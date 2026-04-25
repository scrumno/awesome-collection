<?php

declare(strict_types=1);

namespace DigitalCollective\YandexCaptcha\Domain\ValueObject;

use DigitalCollective\YandexCaptcha\Domain\Exception\EmptyYandexCaptchaTokenException;

final readonly class YandexCaptchaToken
{
    public function __construct(public string $value)
    {
        if ($value === '') throw EmptyYandexCaptchaTokenException::create();
    }
}
