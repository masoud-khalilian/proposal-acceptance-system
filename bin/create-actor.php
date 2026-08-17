<?php

declare(strict_types=1);

// CLI helper for creating reviewer/admin accounts, since (matching the
// original app's design) only submitters can self-register through the web UI.
//
// Usage:
//   php bin/create-actor.php <role: reviewer|admin> <username> <password> <first_name> <last_name> [capacity]

use App\Config\Database;
use App\Repository\RoleRepository;

require __DIR__ . '/../vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();

[, $role, $username, $password, $firstName, $lastName] = $argv + [null, null, null, null, null, null];
$capacity = isset($argv[6]) ? (int) $argv[6] : null;

if (!in_array($role, ['reviewer', 'admin'], true) || $username === null || $password === null) {
    fwrite(STDERR, "Usage: php bin/create-actor.php <reviewer|admin> <username> <password> <first_name> <last_name> [capacity]\n");
    exit(1);
}

$db = Database::connection();
$roleRow = (new RoleRepository($db))->findByKey($role);

if ($roleRow === null) {
    fwrite(STDERR, "Role '$role' is not seeded. Run migrations first.\n");
    exit(1);
}

$stmt = $db->prepare(
    'INSERT INTO actors (username, password_hash, first_name, last_name, role_id, capacity)
     VALUES (:username, :password_hash, :first_name, :last_name, :role_id, :capacity)'
);
$stmt->execute([
    'username' => $username,
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    'first_name' => $firstName ?? '',
    'last_name' => $lastName ?? '',
    'role_id' => $roleRow['id'],
    'capacity' => $role === 'reviewer' ? ($capacity ?? 3) : null,
]);

echo "Created $role '$username'.\n";
