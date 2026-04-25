<?php

declare(strict_types=1);

use DigitalCollective\YandexCaptcha\Application\VerifyCaptchaToken\Handler;
use DigitalCollective\YandexCaptcha\Domain\Service\CaptchaVerifierInterface;
use DigitalCollective\YandexCaptcha\Infrastructure\Http\YandexCaptchaVerifier;
use DigitalCollective\YandexCaptcha\Presentation\Http\Controller\VerifyCaptchaController;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services   = $container->services()
        ->defaults()
        ->autowire(false)
        ->autoconfigure(false);

    $services->set(YandexCaptchaVerifier::class)
        ->args([
            service(HttpClientInterface::class),
            '%yandex_captcha.server_key%',
        ]);

    $services->alias(CaptchaVerifierInterface::class, YandexCaptchaVerifier::class);

    $services->set(Handler::class)
        ->args([
            service(CaptchaVerifierInterface::class),
            service(LoggerInterface::class)->nullOnInvalid(),
        ])
        ->public();

    $services->set(VerifyCaptchaController::class)
        ->args([service(Handler::class)])
        ->public()
        ->tag('controller.service_arguments');
};
