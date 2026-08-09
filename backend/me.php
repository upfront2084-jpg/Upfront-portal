<?php
require __DIR__ . '/config.php';

if (empty($_SESSION['role'])) {
    respond(['role' => null]);
}

respond([
    'role' => $_SESSION['role'],
    'id' => $_SESSION['user_id'],
    'name' => $_SESSION['name'],
    'level' => $_SESSION['level'] ?? null,
]);
