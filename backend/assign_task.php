<?php
require __DIR__ . '/config.php';
require_role('teacher');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['error' => 'Método não permitido.'], 405);
}

$in = json_input();
$studentId = (int)($in['studentId'] ?? 0);
$message = trim($in['message'] ?? '');

if (!$studentId) {
    respond(['error' => 'Aluno inválido.'], 400);
}

require_own_student($pdo, $studentId);

$stmt = $pdo->prepare("
    UPDATE students
    SET assigned_task = ?, assigned_task_at = ?
    WHERE id = ?
");
$stmt->execute([$message !== '' ? $message : null, $message !== '' ? date('Y-m-d H:i:s') : null, $studentId]);

respond(['ok' => true]);
