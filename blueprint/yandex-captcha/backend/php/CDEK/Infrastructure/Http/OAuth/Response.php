<?php

namespace DigitalCollective\CDEK\Infrastructure\Http\OAuth;

class Response
{
    public function __construct(
        public string $accessToken,
        public string $jti,
        public int $expiresIn,
    ) {}
}
