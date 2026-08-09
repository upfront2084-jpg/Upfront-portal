<?php
require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['error' => 'Método não permitido.'], 405);
}

$in = json_input();
$role = $in['role'] ?? '';
$login = trim($in['login'] ?? '');
$password = (string)($in['password'] ?? '');

if (!in_array($role, ['admin', 'teacher', 'student'], true) || $login === '' || $password === '') {
    respond(['error' => 'Preencha login e senha.'], 400);
}

$table = $role === 'admin' ? 'admins' : ($role === 'teacher' ? 'teachers' : 'students');
$stmt = $pdo->prepare("SELECT * FROM $table WHERE login = ?");
$stmt->execute([$login]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    respond(['error' => 'Login ou senha inválidos.'], 401);
}

session_regenerate_id(true);
$_SESSION['role'] = $role;
$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['name'] = $user['name'];
$_SESSION['level'] = $role === 'student' ? $user['level'] : null;

respond([
    'role' => $role,
    'id' => (int)$user['id'],
    'name' => $user['name'],
    'level' => $role === 'student' ? $user['level'] : null,
]);
