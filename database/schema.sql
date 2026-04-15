-- Criação do banco de dados EmpreenderRH
CREATE DATABASE IF NOT EXISTS empreender_rh CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE empreender_rh;

-- Tabela base para autenticação (Admin, Empresa, Candidato)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'empresa', 'candidato') NOT NULL,
    status ENUM('ativo', 'inativo', 'banido') DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Perfil das Empresas
CREATE TABLE empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    razao_social VARCHAR(255) NOT NULL,
    nome_fantasia VARCHAR(255),
    cnpj VARCHAR(18) NOT NULL UNIQUE,
    descricao TEXT,
    website VARCHAR(255),
    logo VARCHAR(255),
    telefone VARCHAR(20),
    endereco VARCHAR(255),
    cidade VARCHAR(100),
    estado CHAR(2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Perfil dos Candidatos
CREATE TABLE candidatos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nome_completo VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    data_nascimento DATE,
    resumo_profissional TEXT,
    curriculo_url VARCHAR(255),
    telefone VARCHAR(20),
    cidade VARCHAR(100),
    estado CHAR(2),
    linkedin_url VARCHAR(255),
    portfolio_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela de Vagas
CREATE TABLE vagas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    requisitos TEXT NOT NULL,
    diferenciais TEXT,
    beneficios TEXT,
    salario DECIMAL(10, 2),
    mostrar_salario BOOLEAN DEFAULT TRUE,
    tipo_contrato ENUM('CLT', 'PJ', 'Estagio', 'Trainee', 'Freelancer', 'Temporario') NOT NULL,
    modalidade ENUM('Presencial', 'Remoto', 'Híbrido') NOT NULL,
    cidade VARCHAR(100),
    estado CHAR(2),
    status ENUM('aberta', 'pausada', 'encerrada', 'cancelada') DEFAULT 'aberta',
    data_expiracao DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Candidaturas (Candidato)
CREATE TABLE candidaturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vaga_id INT NOT NULL,
    candidato_id INT NOT NULL,
    status ENUM('pendente', 'em_analise', 'entrevista', 'aprovado', 'reprovado') DEFAULT 'pendente',
    mensagem_apresentacao TEXT,
    data_candidatura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (vaga_id, candidato_id),
    FOREIGN KEY (vaga_id) REFERENCES vagas(id) ON DELETE CASCADE,
    FOREIGN KEY (candidato_id) REFERENCES candidatos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Vagas Favoritas do Candidato
CREATE TABLE favoritos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidato_id INT NOT NULL,
    vaga_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (candidato_id, vaga_id),
    FOREIGN KEY (candidato_id) REFERENCES candidatos(id) ON DELETE CASCADE,
    FOREIGN KEY (vaga_id) REFERENCES vagas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Inserção de um Administrador padrão (senha: caducadu)
INSERT INTO users (email, password_hash, role) 
VALUES ('cadu@empreenderrh.com', '$2y$10$KG6nUOvdHt3lLJ8EZntciOHtQkxP1kBLQKIlQdjN/gCHf.B4ISfJS', 'admin');
