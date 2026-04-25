<?php

namespace DigitalCollective\YandexCaptcha\Infrastructure\Http;

class Response
{
    public function __construct(
        public bool $isSuccess,
        public string $message = '',
    ) {}
}
