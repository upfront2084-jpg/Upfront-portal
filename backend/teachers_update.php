<?php
require __DIR__ . '/config.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['error' => 'Método não permitido.'], 405);
}

$in = json_input();
$id = (int)($in['id'] ?? 0);
$name = trim($in['name'] ?? '');
$login = trim($in['login'] ?? '');
$password = (string)($in['password'] ?? '');

if (!$id || $name === '' || $login === '') {
    respond(['error' => 'Preencha nome e login.'], 400);
}

$stmt = $pdo->prepare("SELECT id FROM teachers WHERE login = ? AND id != ?");
$stmt->execute([$login, $id]);
if ($stmt->fetch()) {
    respond(['error' => 'Já existe outro professor com esse login.'], 409);
}

if ($password !== '') {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE teachers SET name = ?, login = ?, password_hash = ? WHERE id = ?");
    $stmt->execute([$name, $login, $hash, $id]);
} else {
    $stmt = $pdo->prepare("UPDATE teachers SET name = ?, login = ? WHERE id = ?");
    $stmt->execute([$name, $login, $id]);
}

respond(['ok' => true]);
