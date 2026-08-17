<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repository\SubmissionRepository;
use App\Repository\SubmissionReviewerRepository;
use App\Service\WorkflowService;
use App\Support\Translator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

final class ReviewerController extends Controller
{
    public function __construct(
        Twig $view,
        Translator $translator,
        private readonly SubmissionReviewerRepository $submissionReviewers,
        private readonly SubmissionRepository $submissions,
        private readonly WorkflowService $workflow,
    ) {
        parent::__construct($view, $translator);
    }

    public function dashboard(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, $request, 'submissions/dashboard_reviewer.twig', [
            'assignments' => $this->submissionReviewers->listAssignedToReviewer($this->currentActorId()),
        ]);
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $submissionId = (int) $args['id'];

        if (!$this->workflow->reviewerOwnsAssignment($submissionId, $this->currentActorId())) {
            $response->getBody()->write($this->translator->translate('errors.forbidden'));
            return $response->withStatus(403);
        }

        $submission = $this->submissions->findById($submissionId);
        if ($submission === null) {
            return $response->withStatus(404);
        }

        return $this->render($response, $request, 'submissions/show.twig', [
            'submission' => $submission,
            'reviewers' => $this->submissionReviewers->listForSubmission($submissionId),
            'my_assignment' => $this->submissionReviewers->find($submissionId, $this->currentActorId()),
        ]);
    }

    public function decide(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $submissionId = (int) $args['id'];
        $reviewerId = $this->currentActorId();

        if (!$this->workflow->reviewerOwnsAssignment($submissionId, $reviewerId)) {
            $response->getBody()->write($this->translator->translate('errors.forbidden'));
            return $response->withStatus(403);
        }

        $body = (array) $request->getParsedBody();
        $action = (string) ($body['action'] ?? '');
        $comment = trim((string) ($body['comment'] ?? ''));

        if ($action === 'approve') {
            $this->workflow->approve($submissionId, $reviewerId);
            $this->flash('success', $this->translator->translate('review.approved'));
        } elseif ($action === 'request_changes' && $comment !== '') {
            $this->workflow->requestChanges($submissionId, $reviewerId, $comment);
            $this->flash('success', $this->translator->translate('review.changes_requested'));
        } else {
            $this->flash('error', $this->translator->translate('review.invalid_action'));
        }

        return $this->redirect($response, '/reviewer');
    }
}
