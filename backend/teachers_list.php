<?php
require __DIR__ . '/config.php';
require_role('admin');

$stmt = $pdo->query("SELECT id, name, login FROM teachers ORDER BY name");
respond(['teachers' => $stmt->fetchAll()]);
