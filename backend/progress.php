<?php
require __DIR__ . '/config.php';

if (empty($_SESSION['role'])) {
    respond(['error' => 'Não autorizado.'], 401);
}

if ($_SESSION['role'] === 'teacher') {
    $studentId = (int)($_GET['studentId'] ?? 0);
    if (!$studentId) {
        respond(['error' => 'studentId é obrigatório.'], 400);
    }
} else {
    $studentId = (int)$_SESSION['user_id'];
}

$unlockedStmt = $pdo->prepare("SELECT class_key FROM unlocks WHERE student_id = ?");
$unlockedStmt->execute([$studentId]);
$unlocked = $unlockedStmt->fetchAll(PDO::FETCH_COLUMN);

$completedStmt = $pdo->prepare("SELECT class_key FROM submissions WHERE student_id = ?");
$completedStmt->execute([$studentId]);
$completed = $completedStmt->fetchAll(PDO::FETCH_COLUMN);

$taskStmt = $pdo->prepare("SELECT assigned_task, assigned_task_at FROM students WHERE id = ?");
$taskStmt->execute([$studentId]);
$taskRow = $taskStmt->fetch();

respond([
    'unlocked' => $unlocked,
    'completed' => $completed,
    'assignedTask' => $taskRow ? $taskRow['assigned_task'] : null,
    'assignedTaskAt' => $taskRow ? $taskRow['assigned_task_at'] : null,
]);
