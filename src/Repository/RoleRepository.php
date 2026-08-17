<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class RoleRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function findByKey(string $key): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM roles WHERE key = :key');
        $stmt->execute(['key' => $key]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }
}
