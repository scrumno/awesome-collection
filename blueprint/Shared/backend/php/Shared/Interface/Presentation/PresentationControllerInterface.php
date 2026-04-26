<?php

declare(strict_types=1);

namespace DigitalCollective\Shared\Interface\Presentation;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface PresentationControllerInterface
{
    public function __invoke(Request $request): JsonResponse;
}
