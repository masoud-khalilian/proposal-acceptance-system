<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class SubmissionReviewerRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    /** @param int[] $reviewerIds */
    public function assign(int $submissionId, array $reviewerIds): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO submission_reviewers (submission_id, reviewer_id) VALUES (:submission_id, :reviewer_id)'
        );

        foreach ($reviewerIds as $reviewerId) {
            $stmt->execute(['submission_id' => $submissionId, 'reviewer_id' => (int) $reviewerId]);
        }
    }

    public function find(int $submissionId, int $reviewerId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM submission_reviewers WHERE submission_id = :submission_id AND reviewer_id = :reviewer_id'
        );
        $stmt->execute(['submission_id' => $submissionId, 'reviewer_id' => $reviewerId]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /** @return array<int, array<string, mixed>> pending/changes_requested submissions assigned to this reviewer */
    public function listAssignedToReviewer(int $reviewerId): array
    {
        $stmt = $this->db->prepare(
            "SELECT sr.*, s.title, s.content, s.status AS submission_status, s.created_at AS submitted_at,
                    a.first_name, a.last_name, a.username AS submitter_username, a.profile AS submitter_profile
             FROM submission_reviewers sr
             JOIN submissions s ON s.id = sr.submission_id
             JOIN actors a ON a.id = s.submitter_id
             WHERE sr.reviewer_id = :reviewer_id
             ORDER BY sr.decided_at IS NOT NULL, s.created_at DESC"
        );
        $stmt->execute(['reviewer_id' => $reviewerId]);

        return $stmt->fetchAll();
    }

    /** @return array<int, array<string, mixed>> */
    public function listForSubmission(int $submissionId): array
    {
        $stmt = $this->db->prepare(
            'SELECT sr.*, a.first_name, a.last_name, a.username, a.profile
             FROM submission_reviewers sr
             JOIN actors a ON a.id = sr.reviewer_id
             WHERE sr.submission_id = :submission_id'
        );
        $stmt->execute(['submission_id' => $submissionId]);

        return $stmt->fetchAll();
    }

    public function recordDecision(int $submissionId, int $reviewerId, string $decision, ?string $comment): void
    {
        $stmt = $this->db->prepare(
            'UPDATE submission_reviewers
             SET decision = :decision, comment = :comment, decided_at = now()
             WHERE submission_id = :submission_id AND reviewer_id = :reviewer_id'
        );
        $stmt->execute([
            'decision' => $decision,
            'comment' => $comment,
            'submission_id' => $submissionId,
            'reviewer_id' => $reviewerId,
        ]);
    }

    public function withdrawOtherPendingReviewers(int $submissionId, int $exceptReviewerId): void
    {
        $stmt = $this->db->prepare(
            "UPDATE submission_reviewers
             SET decision = 'withdrawn', decided_at = now()
             WHERE submission_id = :submission_id AND reviewer_id != :reviewer_id AND decision = 'pending'"
        );
        $stmt->execute(['submission_id' => $submissionId, 'reviewer_id' => $exceptReviewerId]);
    }
}
