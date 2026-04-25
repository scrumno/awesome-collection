<?php

declare(strict_types=1);

namespace DigitalCollective\YandexCaptcha\Bridge\Symfony;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * Подключение в config/bundles.php:
 *
 *     return [
 *         // ...
 *         DigitalCollective\YandexCaptcha\Bridge\Symfony\YandexCaptchaBundle::class => ['all' => true],
 *     ];
 *
 * Конфиг в config/packages/yandex_captcha.yaml:
 *
 *     yandex_captcha:
 *         server_key: '%env(YANDEX_CAPTCHA_SERVER_KEY)%'
 *
 * Роуты в config/routes.yaml:
 *
 *     yandex_captcha:
 *         resource: '@YandexCaptchaBundle/Resources/config/routes.php'
 *         prefix: '/api/captcha'
 */
final class YandexCaptchaBundle extends AbstractBundle
{
    protected string $extensionAlias = 'yandex_captcha';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('server_key')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->info('Server-side Yandex SmartCaptcha key')
                ->end()
            ->end();
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->setParameter('yandex_captcha.server_key', $config['server_key']);

        $container->import('Resources/config/services.php');
    }
}
