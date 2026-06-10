-- ============================================================
-- CheckInd — Sistema de Checklist Industrial
-- Indústria Metalúrgica TechForge
-- Schema do Banco de Dados
-- ============================================================

CREATE DATABASE IF NOT EXISTS checkind
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE checkind;

-- ------------------------------------------------------------
-- Gestores (acesso ao painel web)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Funcionários (acesso ao app mobile)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS employees (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    matricula   VARCHAR(50)   NOT NULL UNIQUE,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    sector      VARCHAR(100)  DEFAULT NULL,
    active      TINYINT(1)    NOT NULL DEFAULT 1,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tokens de API para sessões dos funcionários
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS api_tokens (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT           NOT NULL,
    token       VARCHAR(64)   NOT NULL UNIQUE,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Máquinas cadastradas
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS machines (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100) NOT NULL,
    model           VARCHAR(100) DEFAULT NULL,
    sector          VARCHAR(100) DEFAULT NULL,
    qr_code_token   VARCHAR(64)  NOT NULL UNIQUE,
    active          TINYINT(1)   NOT NULL DEFAULT 1,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Itens de checklist (perguntas)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS checklist_items (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    question    TEXT          NOT NULL,
    type        ENUM('entry','exit') NOT NULL,
    active      TINYINT(1)    NOT NULL DEFAULT 1,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Logs de uso — IMUTÁVEIS (sem UPDATE/DELETE permitido)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usage_logs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT  NOT NULL,
    machine_id  INT  NOT NULL,
    action_type ENUM('entry','exit') NOT NULL,
    status      ENUM('approved','rejected') NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (machine_id)  REFERENCES machines(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Respostas individuais de cada item do checklist
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS checklist_responses (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    log_id  INT NOT NULL,
    item_id INT NOT NULL,
    answer  TINYINT(1) NOT NULL COMMENT '1 = Sim / Conforme, 0 = Não / Não Conforme',
    FOREIGN KEY (log_id)  REFERENCES usage_logs(id),
    FOREIGN KEY (item_id) REFERENCES checklist_items(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
