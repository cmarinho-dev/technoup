CREATE DATABASE IF NOT EXISTS technoup;
USE technoup;

CREATE TABLE IF NOT EXISTS consumidor (
	id INT AUTO_INCREMENT PRIMARY KEY,
	nome VARCHAR(200),
	email VARCHAR(50),
	senha VARCHAR(50),
	ativo TINYINT(4) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS lojista (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_loja VARCHAR(100),
    logradouro VARCHAR(150),
    nome_lojista VARCHAR(100),
    cpf VARCHAR(11),
    cnpj VARCHAR(14),
    cep_lojista VARCHAR(8),
    estado VARCHAR(2),
    cidade VARCHAR(100),
    bairro VARCHAR(100),
    numero VARCHAR(10),
    genero VARCHAR(20),
    email VARCHAR(100),
    senha VARCHAR(255),
    telefone VARCHAR(15),
    ativo TINYINT(4) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS produto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    preco DECIMAL(10,2),
    tipo VARCHAR(50),
    descricao TEXT,
    modelo VARCHAR(50),
    marca VARCHAR(50),
    capacidade VARCHAR(50),
    tipodamemoria VARCHAR(50),
    conector VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS catalogo (
	id INT AUTO_INCREMENT PRIMARY KEY,
	produto_id INT NOT NULL,
	lojista_id INT NOT NULL,
	preco DECIMAL(10,2),
	FOREIGN KEY (produto_id) REFERENCES produto(id),
	FOREIGN KEY (lojista_id) REFERENCES lojista(id)
);

INSERT IGNORE INTO lojista 
(id, nome_loja, logradouro, nome_lojista, cpf, cnpj, cep_lojista, estado, cidade, bairro, numero, genero, email, senha, telefone, ativo) 
VALUES
(1, 'Pixaukachu', 'Rua das Flores', 'João Silva', '12345678901', '12345678000199', '01001000', 'PR', 'Curitiba', 'Centro', '100', 'Masculino', 'poo@g.co', 'poo', '11999999999', 1),
(2, 'The Market.', 'Av. Paulista', 'Maria Souza', '98765432100', '98765432000188', '01311000', 'PR', 'Curitiba', 'Barreirinha', '2000', 'Feminino', 'loja@example.com', 'loja', '11888888888', 1);