<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Translator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\Twig;

final class LocaleMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly Translator $translator, private readonly Twig $view)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $requested = $request->getQueryParams()['lang'] ?? null;

        if ($requested !== null) {
            $_SESSION['locale'] = $requested;
        }

        $this->translator->setLocale($_SESSION['locale'] ?? null);

        $this->view->getEnvironment()->addGlobal('locale', $this->translator->locale());
        $this->view->getEnvironment()->addGlobal('direction', $this->translator->direction());
        $this->view->getEnvironment()->addGlobal('current_actor', [
            'id' => $_SESSION['actor_id'] ?? null,
            'name' => $_SESSION['actor_name'] ?? null,
            'role_key' => $_SESSION['role_key'] ?? null,
        ]);

        return $handler->handle($request);
    }
}
