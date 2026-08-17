<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class WorkflowTypeRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function findByKey(string $key): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM workflow_types WHERE key = :key');
        $stmt->execute(['key' => $key]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        return $this->db->query('SELECT * FROM workflow_types ORDER BY id')->fetchAll();
    }
}
