<?php
require __DIR__ . '/config.php';
require_role('student');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['error' => 'Método não permitido.'], 405);
}

$in = json_input();
$classKey = trim($in['classKey'] ?? '');
$etapaNum = (int)($in['etapaNum'] ?? 0);
$answers = $in['answers'] ?? null;

if ($classKey === '' || !$etapaNum || !is_array($answers)) {
    respond(['error' => 'Dados incompletos.'], 400);
}

$stmt = $pdo->prepare("
    INSERT INTO submissions (student_id, class_key, etapa_num, answers_json, submitted_at)
    VALUES (?, ?, ?, ?, NOW())
    ON DUPLICATE KEY UPDATE
        answers_json = VALUES(answers_json),
        submitted_at = NOW(),
        reviewed_at = NULL,
        teacher_feedback = NULL
");
$stmt->execute([
    $_SESSION['user_id'],
    $classKey,
    $etapaNum,
    json_encode($answers, JSON_UNESCAPED_UNICODE),
]);

respond(['ok' => true]);
