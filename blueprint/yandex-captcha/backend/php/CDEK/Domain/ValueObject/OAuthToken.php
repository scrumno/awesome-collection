<?php

namespace DigitalCollective\CDEK\Domain\ValueObject;

use DigitalCollective\CDEK\Domain\Exception\OAuth\OAuthEmptyTokenException;

class OAuthToken
{
    public string $value;
    public function __construct(
        string $value
    ) {
        if (!$value) throw OAuthEmptyTokenException::create();

        $this->value = $value;
    }
}
