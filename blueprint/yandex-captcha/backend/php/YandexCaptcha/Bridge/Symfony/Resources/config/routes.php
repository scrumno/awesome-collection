<?php

declare(strict_types=1);

use DigitalCollective\YandexCaptcha\Presentation\Http\Controller\VerifyCaptchaController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * Подключение в config/routes.yaml целевого проекта:
 *
 *     yandex_captcha:
 *         resource: '@YandexCaptchaBundle/Resources/config/routes.php'
 *         prefix: '/api/captcha'
 *
 * Префикс задаётся проектом — модуль про него ничего не знает.
 */
return static function (RoutingConfigurator $routes): void {
    $routes->add('yandex_captcha_verify', '/verify')
        ->controller(VerifyCaptchaController::class)
        ->methods(['POST']);
};
