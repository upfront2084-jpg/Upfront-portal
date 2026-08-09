<?php
require __DIR__ . '/config.php';
require_role('teacher');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['error' => 'Método não permitido.'], 405);
}

$in = json_input();
$studentId = (int)($in['studentId'] ?? 0);
$classKey = trim($in['classKey'] ?? '');
$unlocked = !empty($in['unlocked']);

if (!$studentId || $classKey === '') {
    respond(['error' => 'Dados incompletos.'], 400);
}

if ($unlocked) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO unlocks (student_id, class_key) VALUES (?, ?)");
    $stmt->execute([$studentId, $classKey]);
} else {
    $stmt = $pdo->prepare("DELETE FROM unlocks WHERE student_id = ? AND class_key = ?");
    $stmt->execute([$studentId, $classKey]);
}

respond(['ok' => true]);
