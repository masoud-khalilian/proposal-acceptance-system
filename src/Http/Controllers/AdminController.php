<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repository\ActorRepository;
use App\Repository\SubmissionRepository;
use App\Support\Translator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

final class AdminController extends Controller
{
    public function __construct(
        Twig $view,
        Translator $translator,
        private readonly ActorRepository $actors,
        private readonly SubmissionRepository $submissions,
    ) {
        parent::__construct($view, $translator);
    }

    public function dashboard(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, $request, 'admin/dashboard.twig', [
            'actors' => $this->actors->all(),
            'submissions' => $this->submissions->all(),
        ]);
    }
}
