<?php
require __DIR__ . '/config.php';
require_role(['teacher', 'admin']);

if ($_SESSION['role'] === 'teacher') {
    $stmt = $pdo->prepare("
        SELECT id, name, login, level, teacher_id, assigned_task, assigned_task_at
        FROM students
        WHERE teacher_id = ?
        ORDER BY level, name
    ");
    $stmt->execute([$_SESSION['user_id']]);
} else {
    $stmt = $pdo->query("
        SELECT s.id, s.name, s.login, s.level, s.teacher_id, t.name AS teacher_name
        FROM students s
        LEFT JOIN teachers t ON t.id = s.teacher_id
        ORDER BY s.level, s.name
    ");
}
respond(['students' => $stmt->fetchAll()]);
