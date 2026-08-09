<?php
require __DIR__ . '/config.php';

if (empty($_SESSION['role'])) {
    respond(['error' => 'Não autorizado.'], 401);
}

$classKey = trim($_GET['classKey'] ?? '');
if ($classKey === '') {
    respond(['error' => 'classKey é obrigatório.'], 400);
}

if ($_SESSION['role'] === 'teacher') {
    $studentId = (int)($_GET['studentId'] ?? 0);
    if (!$studentId) {
        respond(['error' => 'studentId é obrigatório.'], 400);
    }
    require_own_student($pdo, $studentId);
} else {
    $studentId = (int)$_SESSION['user_id'];
}

$stmt = $pdo->prepare("
    SELECT etapa_num, answers_json, submitted_at, reviewed_at, teacher_feedback
    FROM submissions
    WHERE student_id = ? AND class_key = ?
    ORDER BY etapa_num
");
$stmt->execute([$studentId, $classKey]);
$rows = $stmt->fetchAll();

respond(['submissions' => array_map(function ($row) {
    return [
        'etapaNum' => (int)$row['etapa_num'],
        'answers' => json_decode($row['answers_json'], true),
        'submittedAt' => $row['submitted_at'],
        'reviewedAt' => $row['reviewed_at'],
        'feedback' => $row['teacher_feedback'],
    ];
}, $rows)]);
