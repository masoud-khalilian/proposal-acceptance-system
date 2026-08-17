<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ActorRepository;
use App\Repository\RoleRepository;
use InvalidArgumentException;
use RuntimeException;

final class AuthService
{
    public function __construct(
        private readonly ActorRepository $actors,
        private readonly RoleRepository $roles,
    ) {
    }

    public function attempt(string $username, string $password, string $expectedRoleKey): ?array
    {
        $actor = $this->actors->findByUsername($username);

        if ($actor === null || $actor['role_key'] !== $expectedRoleKey) {
            return null;
        }

        if (!password_verify($password, $actor['password_hash'])) {
            return null;
        }

        return $actor;
    }

    public function registerSubmitter(
        string $username,
        string $password,
        string $firstName,
        string $lastName,
        array $profile
    ): int {
        $role = $this->roles->findByKey('submitter');
        if ($role === null) {
            throw new RuntimeException('submitter role is not seeded');
        }

        if ($this->actors->findByUsername($username) !== null) {
            throw new InvalidArgumentException('username_taken');
        }

        return $this->actors->create(
            $username,
            password_hash($password, PASSWORD_DEFAULT),
            $firstName,
            $lastName,
            (int) $role['id'],
            $profile
        );
    }
}
