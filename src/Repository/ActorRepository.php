<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class ActorRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, r.key AS role_key, r.label_fa AS role_label_fa, r.label_en AS role_label_en
             FROM actors a JOIN roles r ON r.id = a.role_id
             WHERE a.username = :username'
        );
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, r.key AS role_key, r.label_fa AS role_label_fa, r.label_en AS role_label_en
             FROM actors a JOIN roles r ON r.id = a.role_id
             WHERE a.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function create(
        string $username,
        string $passwordHash,
        string $firstName,
        string $lastName,
        int $roleId,
        array $profile = [],
        ?int $capacity = null
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO actors (username, password_hash, first_name, last_name, role_id, capacity, profile)
             VALUES (:username, :password_hash, :first_name, :last_name, :role_id, :capacity, :profile)
             RETURNING id'
        );
        $stmt->execute([
            'username' => $username,
            'password_hash' => $passwordHash,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'role_id' => $roleId,
            'capacity' => $capacity,
            'profile' => json_encode($profile, JSON_UNESCAPED_UNICODE),
        ]);

        return (int) $stmt->fetchColumn();
    }

    /** @return array<int, array<string, mixed>> */
    public function listReviewersWithCapacity(): array
    {
        $stmt = $this->db->query(
            "SELECT a.* FROM actors a
             JOIN roles r ON r.id = a.role_id
             WHERE r.key = 'reviewer' AND COALESCE(a.capacity, 0) > 0
             ORDER BY a.last_name"
        );

        return $stmt->fetchAll();
    }

    public function decrementCapacity(int $actorId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE actors SET capacity = GREATEST(COALESCE(capacity, 0) - 1, 0) WHERE id = :id'
        );
        $stmt->execute(['id' => $actorId]);
    }

    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT a.*, r.key AS role_key, r.label_fa AS role_label_fa, r.label_en AS role_label_en
             FROM actors a JOIN roles r ON r.id = a.role_id
             ORDER BY a.created_at DESC'
        );

        return $stmt->fetchAll();
    }
}
