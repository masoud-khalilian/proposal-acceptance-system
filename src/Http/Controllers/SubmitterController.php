<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repository\ActorRepository;
use App\Repository\SubmissionRepository;
use App\Repository\WorkflowTypeRepository;
use App\Service\WorkflowService;
use App\Support\Translator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Slim\Views\Twig;

final class SubmitterController extends Controller
{
    public function __construct(
        Twig $view,
        Translator $translator,
        private readonly SubmissionRepository $submissions,
        private readonly ActorRepository $actors,
        private readonly WorkflowTypeRepository $workflowTypes,
        private readonly WorkflowService $workflow,
    ) {
        parent::__construct($view, $translator);
    }

    public function dashboard(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, $request, 'submissions/dashboard_submitter.twig', [
            'submissions' => $this->submissions->listBySubmitter($this->currentActorId()),
            'reviewers' => $this->actors->listReviewersWithCapacity(),
            'workflow_types' => $this->workflowTypes->all(),
        ]);
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = (array) $request->getParsedBody();
        $title = trim((string) ($body['title'] ?? ''));
        $content = trim((string) ($body['content'] ?? ''));
        $workflowTypeKey = (string) ($body['workflow_type'] ?? '');
        $reviewerIds = array_map('intval', (array) ($body['reviewer_ids'] ?? []));

        if ($title === '' || $content === '' || $workflowTypeKey === '') {
            $this->flash('error', $this->translator->translate('submission.fill_all_fields'));
            return $this->redirect($response, '/submitter');
        }

        try {
            $this->workflow->createSubmission($this->currentActorId(), $workflowTypeKey, $title, $content, $reviewerIds);
        } catch (RuntimeException) {
            $this->flash('error', $this->translator->translate('submission.at_least_one_reviewer'));
            return $this->redirect($response, '/submitter');
        }

        $this->flash('success', $this->translator->translate('submission.created'));
        return $this->redirect($response, '/submitter');
    }
}
