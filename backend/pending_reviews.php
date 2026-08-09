<?php
require __DIR__ . '/config.php';
require_role('teacher');

$stmt = $pdo->prepare("
    SELECT sub.student_id, s.name AS student_name, s.level, sub.class_key, sub.etapa_num, sub.submitted_at
    FROM submissions sub
    JOIN students s ON s.id = sub.student_id
    WHERE s.teacher_id = ? AND sub.reviewed_at IS NULL
    ORDER BY sub.submitted_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$rows = $stmt->fetchAll();

respond(['count' => count($rows), 'pending' => $rows]);
