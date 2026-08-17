<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

final class RoleMiddleware implements MiddlewareInterface
{
    /** @param string[] $allowedRoles */
    public function __construct(private readonly array $allowedRoles)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!in_array($_SESSION['role_key'] ?? null, $this->allowedRoles, true)) {
            $response = new Response();
            $response->getBody()->write('403 Forbidden');
            return $response->withStatus(403);
        }

        return $handler->handle($request);
    }
}
