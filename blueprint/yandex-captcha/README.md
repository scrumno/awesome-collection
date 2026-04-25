# Yandex SmartCaptcha — Symfony Bundle (DDD blueprint)

Готовый к копированию модуль для интеграции Yandex SmartCaptcha в Symfony 7.4 LTS / 8.0.
Namespace: `DigitalCollective\`. PSR-4 на `backend/php/`.

## Структура

```
yandex-captcha/
├── composer.json
├── README.md
├── frontend/                            (под фронтенд-часть, пока пусто)
└── backend/
    └── php/
        ├── Shared/
        │   └── Interface/
        │       ├── Application/
        │       │   ├── ApplicationCommandInterface.php   (маркер CQRS-команды)
        │       │   └── ApplicationQueryInterface.php     (маркер CQRS-запроса)
        │       └── Presentation/
        │           ├── PresentationCommandInterface.php  (маркер DTO с побочкой)
        │           ├── PresentationQueryInterface.php    (маркер read-only DTO)
        │           └── PresentationControllerInterface.php
        └── YandexCaptcha/
            ├── Application/
            │   └── VerifyCaptchaToken/
            │       ├── Command.php
            │       └── Handler.php
            ├── Bridge/
            │   └── Symfony/
            │       ├── YandexCaptchaBundle.php
            │       └── Resources/
            │           └── config/
            │               ├── routes.php
            │               └── services.php
            ├── Domain/
            │   ├── Exception/
            │   │   ├── CaptchaVerificationFailed.php
            │   │   ├── EmptyYandexCaptchaToken.php
            │   │   └── YandexCaptchaException.php
            │   ├── Service/
            │   │   └── CaptchaVerifierInterface.php
            │   └── ValueObject/
            │       ├── VerificationResult.php
            │       └── YandexCaptchaToken.php
            ├── Infrastructure/
            │   └── Http/
            │       └── YandexCaptchaVerifier.php
            └── Presentation/
                └── Http/
                    └── Controller/
                        └── VerifyCaptchaController.php
```

PSR-4: `"DigitalCollective\\": "backend/php/"`.

Так у тебя `backend/php/Shared/...` маппится в `DigitalCollective\Shared\...`,
`backend/php/YandexCaptcha/...` — в `DigitalCollective\YandexCaptcha\...`.
В будущих модулях просто кладёшь новую папку рядом с `YandexCaptcha/`.

## Принцип DDD при привязке к Symfony

Стрелки зависимостей идут только наружу:

```
Domain ← Application ← Infrastructure ← Presentation/Bridge
```

- `Domain/` — чистый PHP, ноль импортов Symfony
- `Application/` — может зависеть от PSR (логгер, кэш) и от маркеров `Shared/Interface/Application`
- `Infrastructure/` — здесь и только здесь живут импорты `Symfony\Contracts\HttpClient`
- `Bridge/Symfony/` — DI и подключение к ядру
- `Presentation/` — `HttpFoundation`

Проверка: открой любой файл из `Domain/` — там не должно быть ни одного `use Symfony\...`.

## Почему `Shared/Interface/` — только маркеры

В Shared лежат **только маркер-интерфейсы** без методов. Раньше там был
`ApplicationHandlerInterface::handle(ApplicationCommandInterface): mixed` —
этого не должно быть. Причины:

1. Жёсткий контракт `handle(ApplicationCommandInterface)` теряет типизацию —
   каждый хендлер вынужден принимать абстрактный интерфейс и делать narrowing
   внутри. IDE и phpstan такой код не помогают писать.
2. Возвращаемый `mixed` — тот же вред с другой стороны.

Вместо этого каждый Handler объявляет свой `__invoke(SpecificCommand): SpecificResult`.
Шина (Symfony Messenger) при необходимости находит хендлеры через `#[AsMessageHandler]`
или через теги — без жёсткого общего интерфейса.

Маркер `ApplicationCommandInterface` остаётся полезным: им можно type-hint'ить
аргумент шины или фильтровать классы при сборке контейнера.

## Установка как пакет в новый проект

В `composer.json` целевого проекта:

```json
{
    "repositories": [
        { "type": "path", "url": "../awesome-collection/blueprint/yandex-captcha" }
    ],
    "require": {
        "digital-collective/yandex-captcha": "*"
    }
}
```

Затем:

```bash
composer require digital-collective/yandex-captcha
```

## Подключение в Symfony проекте

`config/bundles.php`:

```php
return [
    // ...
    captcha\backend\php\YandexCaptcha\Bridge\Symfony\YandexCaptchaBundle::class => ['all' => true],
];
```

`.env`:

```
YANDEX_CAPTCHA_SERVER_KEY=ysc1_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

`config/packages/yandex_captcha.yaml`:

```yaml
yandex_captcha:
    server_key: '%env(YANDEX_CAPTCHA_SERVER_KEY)%'
```

`config/routes.yaml` — префикс задаёт **проект**:

```yaml
yandex_captcha:
    resource: '@YandexCaptchaBundle/Resources/config/routes.php'
    prefix: '/api/captcha'
```

После `bin/console cache:clear` доступен `POST /api/captcha/verify` с body `token=...`.

## Прямое использование use case

Если HTTP-эндпоинт не нужен — инжектишь хендлер напрямую:

```php
use captcha\backend\php\YandexCaptcha\Application\VerifyCaptchaToken\Command;use captcha\backend\php\YandexCaptcha\Application\VerifyCaptchaToken\Handler;use captcha\backend\php\YandexCaptcha\Domain\ValueObject\YandexCaptchaToken;

final class RegisterController
{
    public function __construct(
        private Handler $captcha,
    ) {}

    public function __invoke(Request $request): Response
    {
        $result = ($this->captcha)(new Command(
            token: new YandexCaptchaToken((string) $request->request->get('captcha_token')),
            clientIp: $request->getClientIp(),
        ));

        if (!$result->success) {
            return new JsonResponse(['error' => 'captcha', 'reasons' => $result->errors], 400);
        }

        // ... регистрация
    }
}
```

## Шаблон будущих модулей

Когда будешь делать следующий модуль (например, интеграцию с какой-то платёжкой),
структура та же:

```
backend/php/SomePayment/
├── Application/<UseCase>/
│   ├── Command.php
│   └── Handler.php
├── Bridge/Symfony/
│   ├── SomePaymentBundle.php
│   └── Resources/config/{services,routes}.php
├── Domain/{Exception,Service,ValueObject}/
├── Infrastructure/...
└── Presentation/Http/Controller/
```

`Shared/Interface/` уже есть — используешь те же маркеры, не дублируешь их в каждом модуле.

| Что добавить | Куда |
| --- | --- |
| Новый use case в существующем модуле | `Application/<NewUseCase>/{Command,Handler}.php` |
| Альтернативный провайдер (reCAPTCHA, hCaptcha) | новая реализация `CaptchaVerifierInterface` в `Infrastructure/Http/` |
| Кэш результата | декоратор над `CaptchaVerifierInterface` в `Infrastructure/Cache/` |
| Console команда | `Presentation/Console/<Name>Command.php` |
| Messenger handler | повесить `#[AsMessageHandler]` на существующий `Handler` |
