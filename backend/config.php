<?php
/**
 * Configuração do backend — preencha com os dados do SEU banco MySQL
 * (painel de hospedagem / cPanel costuma mostrar host, nome do banco, usuário e senha).
 */
$DB_HOST = 'localhost';
$DB_NAME = 'troque_para_o_nome_do_seu_banco';
$DB_USER = 'troque_para_o_usuario';
$DB_PASS = 'troque_para_a_senha';

// Se o HTML do portal for aberto em outro domínio/subdomínio diferente deste backend,
// troque abaixo pela URL exata de onde o portal roda (sem barra no final).
// Se o portal e o backend estiverem no MESMO domínio, pode deixar como está.
$ALLOWED_ORIGIN = 'https://SEUDOMINIO.com.br';

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode(['error' => 'Erro de conexão com o banco de dados.']));
}

session_set_cookie_params([
    'lifetime' => 60 * 60 * 24 * 30, // 30 dias
    'path' => '/',
    'samesite' => 'Lax',
]);
session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . $ALLOWED_ORIGIN);
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function json_input(): array {
    $data = json_decode(file_get_contents('php://input'), true);
    return is_array($data) ? $data : [];
}

function respond($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function require_role($roles): void {
    $allowed = is_array($roles) ? $roles : [$roles];
    if (empty($_SESSION['role']) || !in_array($_SESSION['role'], $allowed, true)) {
        respond(['error' => 'Não autorizado.'], 401);
    }
}

function require_own_student(PDO $pdo, int $studentId): void {
    $stmt = $pdo->prepare("SELECT teacher_id FROM students WHERE id = ?");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch();
    if (!$student || (int)$student['teacher_id'] !== (int)$_SESSION['user_id']) {
        respond(['error' => 'Este aluno não está sob sua responsabilidade.'], 403);
    }
}
