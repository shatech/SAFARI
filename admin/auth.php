<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function current_admin(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $pdo = db();

    $stmt = $pdo->prepare(
        'SELECT u.id, u.name, u.email
         FROM users AS u
         INNER JOIN user_roles AS ur ON ur.user_id = u.id
         INNER JOIN roles AS r ON r.id = ur.role_id
         WHERE u.id = :user_id
           AND u.is_active = 1
           AND r.code = :role_code
         LIMIT 1'
    );

    $stmt->execute([
        'user_id'  => (int) $_SESSION['user_id'],
        'role_code' => 'admin',
    ]);

    $admin = $stmt->fetch();

    if (!$admin) {
        unset($_SESSION['user_id']);
        return null;
    }

    return $admin;
}

function require_admin(): array
{
    $admin = current_admin();

    if ($admin === null) {
        header('Location: login.php');
        exit;
    }

    return $admin;
}