
CREATE DATABASE IF NOT EXISTS estoque_coopanest
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

USE estoque_coopanest;

CREATE TABLE IF NOT EXISTS produtos (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    quantidade INT(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS movimentacoes (
    id INT(11) NOT NULL AUTO_INCREMENT,
    produto_id INT(11) NOT NULL,
    tipo ENUM('entrada', 'saida') NOT NULL,
    quantidade INT(11) NOT NULL,
    observacao TEXT,
    data_registro DATETIME NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO produtos (nome, quantidade) VALUES
    ('Seringa Descartavel 10ml', 200),
    ('Luva de Procedimento M', 500),
    ('Mascara Cirurgica', 1000),
    ('Agulha Hipodemica 25x7', 300),
    ('Gaze Esteril 7,5x7,5', 450),
    ('Esparadrapo 10cm x 4,5m', 80),
    ('Alcool 70% 1L', 60),
    ('Soro Fisiologico 500ml', 150);
