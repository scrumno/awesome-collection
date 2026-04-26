# Blueprint Modules Integration Guide

Этот документ описывает, как подключать модули из папки `blueprint` в текущий Symfony-проект.

## 1) Подключение `yandex-captcha` в текущий проект

### Шаг 1. Добавить локальный Composer repository

В `composer.json` целевого проекта добавьте:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "./blueprint",
      "options": {
        "symlink": true
      }
    }
  ]
}
```

Если `repositories` уже есть, просто добавьте туда новый объект.

### Шаг 2. Установить пакет

`blueprint/composer.json` объявляет пакет:

- `name`: `digital-collective/awesome-collection`

Установка:

```bash
composer require digital-collective/awesome-collection:@dev
```

> Если версия позже будет тегирована (например `1.0.0`), используйте обычный `composer require digital-collective/awesome-collection`.

### Шаг 3. Зарегистрировать bundle

`config/bundles.php`:

```php
return [
    // ...
    DigitalCollective\YandexCaptcha\Bridge\Symfony\YandexCaptchaBundle::class => ['all' => true],
];
```

### Шаг 4. Передать ключ SmartCaptcha

`config/packages/yandex_captcha.yaml`:

```yaml
yandex_captcha:
  server_key: '%env(YANDEX_CAPTCHA_SERVER_KEY)%'
```

`.env.local` (или секреты окружения):

```dotenv
YANDEX_CAPTCHA_SERVER_KEY=your_server_key
```

### Шаг 5. Подключить маршруты

`config/routes.yaml`:

```yaml
yandex_captcha:
  resource: '@YandexCaptchaBundle/Resources/config/routes.php'
  prefix: '/api/captcha'
```

### Шаг 6. Проверка

```bash
composer dump-autoload
php bin/console cache:clear
php bin/console debug:router | grep yandex_captcha
```

Если всё подключено верно, появится маршрут `yandex_captcha_verify`.

## 2) Как подключать другие модули из `blueprint`

Сейчас один пакет `digital-collective/awesome-collection` экспортирует несколько namespace:

- `DigitalCollective\Shared\...`
- `DigitalCollective\CDEK\...`
- `DigitalCollective\YandexCaptcha\...`

Это означает, что после установки этого пакета классы из этих пространств имён уже доступны автозагрузчику.

### Добавление нового Symfony bundle-модуля

1. Создать namespace модуля в `blueprint/composer.json` (`autoload.psr-4`).
2. Добавить `Bundle` класс (например `DigitalCollective\NewModule\Bridge\Symfony\NewModuleBundle`).
3. Добавить `Resources/config/services.php` и при необходимости `routes.php`.
4. В целевом проекте:
   - зарегистрировать bundle в `config/bundles.php`;
   - подключить `config/packages/<module>.yaml`;
   - подключить маршруты в `config/routes.yaml` (если есть HTTP endpoints).

## 3) Частые проблемы

- Неверный namespace или путь в `autoload.psr-4`.
- Bundle зарегистрирован, но не подключены routes.
- Нет нужного env (`YANDEX_CAPTCHA_SERVER_KEY`) в окружении.
- Не обновлён autoload (`composer dump-autoload`).

