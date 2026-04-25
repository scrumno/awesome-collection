<?php

declare(strict_types=1);

namespace DigitalCollective\YandexCaptcha\Domain\Exception;

class EmptyYandexCaptchaTokenException extends \InvalidArgumentException implements YandexCaptchaException
{
    public static function create(): self
    {
        return new self('Яндекс капча не может быть пустой');
    }
}
