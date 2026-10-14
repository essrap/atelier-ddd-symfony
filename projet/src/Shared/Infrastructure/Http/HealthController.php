<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Minimal liveness endpoint: the application boots and answers HTTP requests.
 */
final readonly class HealthController
{
    #[Route('/health', name: 'health', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->jsonResponse(['status' => 'ok'], Response::HTTP_OK);
    }

    /**
     * @param array<string, string> $payload
     */
    private function jsonResponse(array $payload, int $statusCode): Response
    {
        return new Response(
            json_encode($payload, JSON_THROW_ON_ERROR),
            $statusCode,
            ['Content-Type' => 'application/json'],
        );
    }
}
