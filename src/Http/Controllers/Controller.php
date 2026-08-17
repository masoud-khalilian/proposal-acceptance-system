<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\Translator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

abstract class Controller
{
    public function __construct(
        protected readonly Twig $view,
        protected readonly Translator $translator,
    ) {
    }

    protected function render(
        ResponseInterface $response,
        ServerRequestInterface $request,
        string $template,
        array $data = []
    ): ResponseInterface {
        $data['csrf_name'] = $request->getAttribute('csrf_name');
        $data['csrf_value'] = $request->getAttribute('csrf_value');
        $data['flash'] = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $this->view->render($response, $template, $data);
    }

    protected function redirect(ResponseInterface $response, string $location): ResponseInterface
    {
        return $response->withHeader('Location', $location)->withStatus(302);
    }

    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function currentActorId(): int
    {
        return (int) $_SESSION['actor_id'];
    }
}
