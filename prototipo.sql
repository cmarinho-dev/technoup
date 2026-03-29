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
	email VARCHAR(50),
	senha VARCHAR(50),
	nome_loja VARCHAR(50),
	telefone INT,
	ativo TINYINT(4) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS produto (
	id INT AUTO_INCREMENT PRIMARY KEY,
	nome VARCHAR(50),
	preco DECIMAL(10,2),
	tipo VARCHAR(30)
);

CREATE TABLE IF NOT EXISTS catalogo (
	id INT AUTO_INCREMENT PRIMARY KEY,
	produto_id INT NOT NULL,
	lojista_id INT NOT NULL,
	preco DECIMAL(10,2),
	FOREIGN KEY (produto_id) REFERENCES produto(id),
	FOREIGN KEY (lojista_id) REFERENCES lojista(id)
);

INSERT IGNORE INTO lojista (id, email, senha, nome_loja, telefone, ativo) VALUES
(1, 'poo@g.co', 'poo', 'Loja Exemplo', 123456789, 1),
(2, 'loja@example.com', 'loja', 'Loja Exemplo 2', 987654321, 1);