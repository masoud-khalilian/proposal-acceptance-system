<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class SubmissionRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function create(int $workflowTypeId, int $submitterId, string $title, string $content): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO submissions (workflow_type_id, submitter_id, title, content)
             VALUES (:workflow_type_id, :submitter_id, :title, :content)
             RETURNING id'
        );
        $stmt->execute([
            'workflow_type_id' => $workflowTypeId,
            'submitter_id' => $submitterId,
            'title' => $title,
            'content' => $content,
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM submissions WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /** @return array<int, array<string, mixed>> */
    public function listBySubmitter(int $submitterId): array
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, wt.label_fa AS workflow_label_fa, wt.label_en AS workflow_label_en
             FROM submissions s
             JOIN workflow_types wt ON wt.id = s.workflow_type_id
             WHERE s.submitter_id = :submitter_id
             ORDER BY s.created_at DESC'
        );
        $stmt->execute(['submitter_id' => $submitterId]);

        return $stmt->fetchAll();
    }

    public function markApproved(int $submissionId, int $approvedBy): void
    {
        $stmt = $this->db->prepare(
            "UPDATE submissions SET status = 'approved', approved_by = :approved_by, approved_at = now()
             WHERE id = :id"
        );
        $stmt->execute(['approved_by' => $approvedBy, 'id' => $submissionId]);
    }

    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT s.*, wt.label_fa AS workflow_label_fa, wt.label_en AS workflow_label_en
             FROM submissions s JOIN workflow_types wt ON wt.id = s.workflow_type_id
             ORDER BY s.created_at DESC'
        );

        return $stmt->fetchAll();
    }
}
