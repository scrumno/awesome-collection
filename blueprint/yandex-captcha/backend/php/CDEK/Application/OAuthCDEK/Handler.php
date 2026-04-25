<?php

namespace DigitalCollective\CDEK\Application\OAuthCDEK;

use DigitalCollective\CDEK\Infrastructure\Http\OAuth\CDEKOAuthService;

class Handler
{
    public function __construct(
        private readonly CDEKOAuthService $authService,
    ) {}

    public function __invoke() {}
}
