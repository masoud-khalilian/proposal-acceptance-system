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
    private const MAX_ATTACHMENT_BYTES = 10 * 1024 * 1024;

    public function __construct(
        Twig $view,
        Translator $translator,
        private readonly SubmissionRepository $submissions,
        private readonly ActorRepository $actors,
        private readonly WorkflowTypeRepository $workflowTypes,
        private readonly WorkflowService $workflow,
        private readonly string $uploadsDir,
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

        $attachment = $request->getUploadedFiles()['attachment'] ?? null;
        if ($attachment !== null && $attachment->getError() === UPLOAD_ERR_OK) {
            if ($attachment->getSize() > self::MAX_ATTACHMENT_BYTES) {
                $this->flash('error', $this->translator->translate('submission.attachment_too_large'));
                return $this->redirect($response, '/submitter');
            }

            // Trust the file's own magic bytes, not the client-supplied
            // extension/MIME type, which are trivially spoofable.
            $stream = $attachment->getStream();
            $header = $stream->read(5);
            $stream->rewind();
            if ($header !== '%PDF-') {
                $this->flash('error', $this->translator->translate('submission.attachment_invalid'));
                return $this->redirect($response, '/submitter');
            }
        } elseif ($attachment !== null && $attachment->getError() !== UPLOAD_ERR_NO_FILE) {
            $this->flash('error', $this->translator->translate('submission.attachment_invalid'));
            return $this->redirect($response, '/submitter');
        }

        try {
            $submissionId = $this->workflow->createSubmission(
                $this->currentActorId(),
                $workflowTypeKey,
                $title,
                $content,
                $reviewerIds
            );
        } catch (RuntimeException) {
            $this->flash('error', $this->translator->translate('submission.at_least_one_reviewer'));
            return $this->redirect($response, '/submitter');
        }

        if ($attachment !== null && $attachment->getError() === UPLOAD_ERR_OK) {
            $attachment->moveTo($this->uploadsDir . '/' . $submissionId . '.pdf');
            $this->submissions->attachFile(
                $submissionId,
                $attachment->getClientFilename() ?? 'attachment.pdf',
                $attachment->getSize()
            );
        }

        $this->flash('success', $this->translator->translate('submission.created'));
        return $this->redirect($response, '/submitter');
    }
}
