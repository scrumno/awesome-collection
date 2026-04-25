<?php

namespace DigitalCollective\CDEK\Domain\Exception\OAuth;

use DigitalCollective\CDEK\Domain\Exception\CDEKExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CDEKOAuthServiceException extends \DomainException implements CDEKExceptionInterface {
    public static function fromResponse(array $data) {
        return new self($data['error'] . ': ' . $data['error_description']);
    }
}
