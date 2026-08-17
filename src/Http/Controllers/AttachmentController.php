<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repository\SubmissionRepository;
use App\Service\WorkflowService;
use App\Support\Translator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

final class AttachmentController extends Controller
{
    public function __construct(
        Twig $view,
        Translator $translator,
        private readonly SubmissionRepository $submissions,
        private readonly WorkflowService $workflow,
        private readonly string $uploadsDir,
    ) {
        parent::__construct($view, $translator);
    }

    public function download(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $submissionId = (int) $args['id'];
        $submission = $this->submissions->findById($submissionId);

        if ($submission === null || $submission['attachment_filename'] === null) {
            return $response->withStatus(404);
        }

        $actorId = $this->currentActorId();
        $roleKey = $_SESSION['role_key'] ?? null;

        $isOwner = $roleKey === 'submitter' && (int) $submission['submitter_id'] === $actorId;
        $isAdmin = $roleKey === 'admin';
        $isAssignedReviewer = $roleKey === 'reviewer' && $this->workflow->reviewerOwnsAssignment($submissionId, $actorId);

        if (!$isOwner && !$isAdmin && !$isAssignedReviewer) {
            $response->getBody()->write($this->translator->translate('errors.forbidden'));
            return $response->withStatus(403);
        }

        $path = $this->uploadsDir . '/' . $submissionId . '.pdf';
        if (!is_file($path)) {
            return $response->withStatus(404);
        }

        $filename = str_replace('"', '', $submission['attachment_filename']);
        $response->getBody()->write((string) file_get_contents($path));

        return $response
            ->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'inline; filename="' . $filename . '"');
    }
}
