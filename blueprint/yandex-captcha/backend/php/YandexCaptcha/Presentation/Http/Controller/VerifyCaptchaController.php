<?php

declare(strict_types=1);

namespace DigitalCollective\YandexCaptcha\Presentation\Http\Controller;

use DigitalCollective\Shared\Interface\Presentation\PresentationControllerInterface;
use DigitalCollective\YandexCaptcha\Application\VerifyCaptchaToken\Command;
use DigitalCollective\YandexCaptcha\Application\VerifyCaptchaToken\Handler;
use DigitalCollective\YandexCaptcha\Domain\Exception\YandexCaptchaException;
use DigitalCollective\YandexCaptcha\Domain\ValueObject\YandexCaptchaToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class VerifyCaptchaController implements PresentationControllerInterface
{
    public function __construct(
        private Handler $handler,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $token = new YandexCaptchaToken((string) ($request->getPayload()->get('token') ?? ''));

            $res = ($this->handler)(new Command(
                token: $token,
                clientIp: $request->getClientIp(),
            ));

            $status = $res->isSuccess ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST;

            return new JsonResponse(
                ['isSuccess' => $res->isSuccess],
                $status
            );
        } catch (YandexCaptchaException $e) {
            return new JsonResponse([
                'success' => false,
                'errors' => [$e->getMessage()],
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
