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
$level = trim($in['level'] ?? '');
$teacherId = !empty($in['teacherId']) ? (int)$in['teacherId'] : null;

if ($name === '' || $login === '' || $password === '' || !in_array($level, ['A1', 'A2', 'B1', 'B2'], true)) {
    respond(['error' => 'Preencha nome, login, senha e um nível válido (A1/A2/B1/B2).'], 400);
}

if ($teacherId !== null) {
    $stmt = $pdo->prepare("SELECT id FROM teachers WHERE id = ?");
    $stmt->execute([$teacherId]);
    if (!$stmt->fetch()) {
        respond(['error' => 'Professor responsável inválido.'], 400);
    }
}

$stmt = $pdo->prepare("SELECT id FROM students WHERE login = ?");
$stmt->execute([$login]);
if ($stmt->fetch()) {
    respond(['error' => 'Já existe um aluno com esse login.'], 409);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO students (name, login, password_hash, level, teacher_id) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$name, $login, $hash, $level, $teacherId]);

respond(['ok' => true, 'id' => (int)$pdo->lastInsertId()]);
