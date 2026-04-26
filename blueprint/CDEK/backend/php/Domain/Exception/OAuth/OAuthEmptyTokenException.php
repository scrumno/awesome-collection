<?php

namespace DigitalCollective\CDEK\Domain\Exception\OAuth;

use DigitalCollective\CDEK\Domain\Exception\CDEKExceptionInterface;

class OAuthEmptyTokenException extends \InvalidArgumentException implements CDEKExceptionInterface {
    public static function create(): self
    {
        return new self('Пустой токен');
    }
}
