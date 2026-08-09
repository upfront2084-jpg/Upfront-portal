<?php
require __DIR__ . '/config.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['error' => 'Método não permitido.'], 405);
}

$in = json_input();
$id = (int)($in['id'] ?? 0);

if (!$id) {
    respond(['error' => 'Aluno inválido.'], 400);
}

$stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
$stmt->execute([$id]);

if ($stmt->rowCount() === 0) {
    respond(['error' => 'Aluno não encontrado.'], 404);
}

respond(['ok' => true]);
