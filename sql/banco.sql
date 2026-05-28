CREATE DATABASE IF NOT EXISTS hotel_reservas
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE hotel_reservas;

CREATE TABLE IF NOT EXISTS quartos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(10) NOT NULL UNIQUE,
    tipo VARCHAR(50) NOT NULL,
    preco_diaria DECIMAL(10,2) NOT NULL,
    capacidade INT NOT NULL DEFAULT 1,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    CONSTRAINT chk_quarto_preco CHECK (preco_diaria > 0),
    CONSTRAINT chk_quarto_capacidade CHECK (capacidade > 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS hospedes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    telefone VARCHAR(20) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospede_id INT NOT NULL,
    quarto_id INT NOT NULL,
    data_entrada DATE NOT NULL,
    data_saida DATE NOT NULL,
    quantidade_pessoas INT NOT NULL,
    status ENUM('ativa', 'cancelada') NOT NULL DEFAULT 'ativa',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reserva_hospede FOREIGN KEY (hospede_id) REFERENCES hospedes(id),
    CONSTRAINT fk_reserva_quarto FOREIGN KEY (quarto_id) REFERENCES quartos(id),
    CONSTRAINT chk_reserva_datas CHECK (data_saida > data_entrada),
    CONSTRAINT chk_reserva_pessoas CHECK (quantidade_pessoas > 0)
) ENGINE=InnoDB;

-- Índice criado explicitamente para melhorar a busca de reservas por quarto e período.
CREATE INDEX idx_reservas_quarto_datas
ON reservas (quarto_id, data_entrada, data_saida);

INSERT INTO quartos (numero, tipo, preco_diaria, capacidade, ativo) VALUES
('101', 'Solteiro', 160.00, 1, 1),
('102', 'Casal', 240.00, 2, 1),
('201', 'Família', 380.00, 4, 1),
('202', 'Luxo', 520.00, 2, 1)
ON DUPLICATE KEY UPDATE
    tipo = VALUES(tipo),
    preco_diaria = VALUES(preco_diaria),
    capacidade = VALUES(capacidade),
    ativo = VALUES(ativo);
