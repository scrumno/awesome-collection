<?php

namespace DigitalCollective\YandexCaptcha\Infrastructure\Http\Enum;

enum StatusType: string
{
    case OK = 'ok';
    case Failed = 'failed';
}
