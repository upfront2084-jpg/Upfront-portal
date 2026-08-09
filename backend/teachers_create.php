<?php
require __DIR__ . '/config.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['error' => 'Método não permitido.'], 405);
}

$in = json_input();
$name = trim($in['name'] ?? '');
$login = trim($in['login'] ?? '');
$password = (string)($in['password'] ?? '');

if ($name === '' || $login === '' || $password === '') {
    respond(['error' => 'Preencha nome, login e senha.'], 400);
}

$stmt = $pdo->prepare("SELECT id FROM teachers WHERE login = ?");
$stmt->execute([$login]);
if ($stmt->fetch()) {
    respond(['error' => 'Já existe um professor com esse login.'], 409);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO teachers (name, login, password_hash) VALUES (?, ?, ?)");
$stmt->execute([$name, $login, $hash]);

respond(['ok' => true, 'id' => (int)$pdo->lastInsertId()]);
