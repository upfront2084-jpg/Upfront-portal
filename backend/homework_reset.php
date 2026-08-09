<?php
require __DIR__ . '/config.php';
require_role('teacher');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['error' => 'Método não permitido.'], 405);
}

$in = json_input();
$studentId = (int)($in['studentId'] ?? 0);
$classKey = trim($in['classKey'] ?? '');
$etapaNum = (int)($in['etapaNum'] ?? 0);

if (!$studentId || $classKey === '' || !$etapaNum) {
    respond(['error' => 'Dados incompletos.'], 400);
}

require_own_student($pdo, $studentId);

$stmt = $pdo->prepare("DELETE FROM submissions WHERE student_id = ? AND class_key = ? AND etapa_num = ?");
$stmt->execute([$studentId, $classKey, $etapaNum]);

if ($stmt->rowCount() === 0) {
    respond(['error' => 'Nenhum envio encontrado para esta etapa.'], 404);
}

respond(['ok' => true]);
