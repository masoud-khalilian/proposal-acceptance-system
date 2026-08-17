<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ActorRepository;
use App\Repository\SubmissionRepository;
use App\Repository\SubmissionReviewerRepository;
use App\Repository\WorkflowTypeRepository;
use RuntimeException;

final class WorkflowService
{
    public function __construct(
        private readonly SubmissionRepository $submissions,
        private readonly SubmissionReviewerRepository $submissionReviewers,
        private readonly WorkflowTypeRepository $workflowTypes,
        private readonly ActorRepository $actors,
    ) {
    }

    /** @param int[] $reviewerIds */
    public function createSubmission(
        int $submitterId,
        string $workflowTypeKey,
        string $title,
        string $content,
        array $reviewerIds
    ): int {
        $workflowType = $this->workflowTypes->findByKey($workflowTypeKey);
        if ($workflowType === null) {
            throw new RuntimeException('unknown_workflow_type');
        }

        if ($reviewerIds === []) {
            throw new RuntimeException('at_least_one_reviewer_required');
        }

        $submissionId = $this->submissions->create((int) $workflowType['id'], $submitterId, $title, $content);
        $this->submissionReviewers->assign($submissionId, $reviewerIds);

        return $submissionId;
    }

    /**
     * A reviewer approves a submission: the submission is marked approved,
     * the reviewer's capacity is decremented, and any other still-pending
     * reviewer assignments for the same submission are withdrawn.
     */
    public function approve(int $submissionId, int $reviewerId): void
    {
        $this->submissionReviewers->recordDecision($submissionId, $reviewerId, 'approved', null);
        $this->submissions->markApproved($submissionId, $reviewerId);
        $this->actors->decrementCapacity($reviewerId);
        $this->submissionReviewers->withdrawOtherPendingReviewers($submissionId, $reviewerId);
    }

    public function requestChanges(int $submissionId, int $reviewerId, string $comment): void
    {
        $this->submissionReviewers->recordDecision($submissionId, $reviewerId, 'changes_requested', $comment);
    }

    public function reviewerOwnsAssignment(int $submissionId, int $reviewerId): bool
    {
        return $this->submissionReviewers->find($submissionId, $reviewerId) !== null;
    }
}
