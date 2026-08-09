-- Upfront Idiomas — banco de dados do portal (login + envio de tarefas)
-- Rode este arquivo uma vez no seu MySQL (phpMyAdmin, Adminer, ou linha de comando)
-- antes de usar os arquivos em /backend.

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  login VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS teachers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  login VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  login VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  level VARCHAR(10) NOT NULL,
  teacher_id INT NULL,                   -- professor responsável por este aluno
  assigned_task TEXT NULL,               -- tarefa específica designada pelo professor
  assigned_task_at DATETIME NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS submissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  class_key VARCHAR(20) NOT NULL,        -- ex: 'A1-1', 'A1-2', 'A1-3'
  etapa_num INT NOT NULL,                -- qual etapa da aula (1, 2, 3...)
  answers_json MEDIUMTEXT NOT NULL,      -- respostas do aluno nesta etapa (JSON)
  submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  reviewed_at DATETIME NULL,
  teacher_feedback TEXT NULL,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  UNIQUE KEY uniq_student_class_etapa (student_id, class_key, etapa_num)  -- reenviar a mesma etapa substitui o envio anterior
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS unlocks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  class_key VARCHAR(20) NOT NULL,        -- ex: 'A1-2', 'A1-3'
  unlocked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  UNIQUE KEY uniq_student_unlock (student_id, class_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- A Class 1 de cada nível fica sempre liberada por padrão (não precisa de linha aqui).
-- As demais aulas só ficam visíveis para o aluno quando o professor libera (via tela "Painel > Alunos").

-- Exemplo de como criar o primeiro (e único) acesso manual: o Admin.
-- Gere o hash com: php -r "echo password_hash('SUA_SENHA_AQUI', PASSWORD_DEFAULT);"
-- INSERT INTO admins (name, login, password_hash) VALUES ('Nome do Admin', 'admin', '$2y$...cole_o_hash_aqui...');

-- A partir daí, tudo o resto (professores E alunos) é cadastrado direto pela tela,
-- entrando como Admin e usando o Painel Admin — não precisa mexer no banco de novo.
