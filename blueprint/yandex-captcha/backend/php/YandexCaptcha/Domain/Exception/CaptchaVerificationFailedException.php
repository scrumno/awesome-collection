<?php

declare(strict_types=1);

namespace DigitalCollective\YandexCaptcha\Domain\Exception;

class CaptchaVerificationFailedException extends \RuntimeException implements YandexCaptchaException
{
    public static function transportError(\Throwable $previous): self
    {
        return new self('Яндекс капча не отвечает', 0, $previous);
    }

    public static function unexpectedStatus(int $code): self
    {
        return new self("Яндекс капча вернула неизвестный статус HTTP: {$code}");
    }

    public static function malformedResponse(string $body): self
    {
        return new self('Яндекс капча вернула некорректный ответ: ' . $body);
    }
}
