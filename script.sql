CREATE DATABASE IF NOT EXISTS technoup;
USE technoup;

CREATE TABLE IF NOT EXISTS conta
(
    id        INT AUTO_INCREMENT PRIMARY KEY,
    nome      VARCHAR(200)                                                       NOT NULL,
    email     VARCHAR(100)                                                       NOT NULL UNIQUE,
    senha     VARCHAR(255)                                                       NOT NULL,
    tipo      ENUM ('consumidor','lojista','administrador') DEFAULT 'consumidor' NOT NULL,
    ativo     TINYINT(1)                                    DEFAULT 1,
    criado_em DATETIME                                      DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS loja
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    conta_id   INT          NOT NULL UNIQUE,
    nome_loja  VARCHAR(100) NOT NULL,
    telefone   VARCHAR(15),
    cpf        VARCHAR(11),
    cnpj       VARCHAR(14)  NOT NULL,
    cep        VARCHAR(8),
    estado     VARCHAR(2),
    cidade     VARCHAR(100),
    bairro     VARCHAR(100),
    logradouro VARCHAR(150),
    numero     VARCHAR(10),
    banner_img VARCHAR(255),
    criado_em  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conta_id) REFERENCES conta (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS produto
(
    id          INT AUTO_INCREMENT PRIMARY KEY,
    loja_id     INT                NOT NULL,
    nome        VARCHAR(100)       NOT NULL,
    preco       DECIMAL(10, 2)     NOT NULL,
    tipo        VARCHAR(50),
    marca       VARCHAR(50),
    modelo      VARCHAR(50),
    descricao   TEXT,
    desconto    INT      DEFAULT 0 NOT NULL,
    preco_final DECIMAL(10, 2) GENERATED ALWAYS AS (preco * (1 - desconto / 100)) VIRTUAL,
    criado_em   DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS imagem_produto
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT          NOT NULL UNIQUE,
    arquivo    VARCHAR(50)  NOT NULL,
    caminho    VARCHAR(255) NOT NULL,
    descricao  VARCHAR(200) DEFAULT 'imagem do produto',
    FOREIGN KEY (produto_id) REFERENCES produto (id) ON DELETE CASCADE
);

INSERT IGNORE INTO conta (id, nome, email, senha, tipo, ativo)
VALUES (1, 'João Silva', 'loja@g.com', 'loja', 'lojista', 1),
       (2, 'Maria Souza', 'maria@example.com', 'senha123', 'lojista', 1),
       (3, 'Pedro Alves', 'pedro@example.com', 'pedro_pass', 'consumidor', 1),
       (4, 'Lucas Martins', 'lucas@g.co', 'lucas', 'lojista', 1),
       (5, 'Administrador', 'admin@g.co', 'admin123', 'administrador', 1);

INSERT IGNORE INTO loja
(id, nome_loja, logradouro, cpf, cnpj, cep, estado, cidade, bairro, numero, telefone, banner_img, conta_id)
VALUES (1, 'Pixau', 'Rua das Flores', '12345678901', '12345678000199', '01001000', 'PR', 'Curitiba', 'Centro', '100',
        '11999999999', '../_arquivosmidia/lojas/pichau.png', 1),
       (2, 'The Market.', 'Av. Paulista', '98765432100', '98765432000188', '01311000', 'PR', 'Curitiba', 'Barreirinha',
        '2000', '11888888888', '../_arquivosmidia/lojas/kabum.jpg', 2);

INSERT IGNORE INTO produto (id, loja_id, nome, preco, tipo, descricao, modelo, marca)
VALUES (1, 1, 'SSD NVMe 1TB', 499.90, 'Armazenamento', 'SSD NVMe M.2 com alta performance', 'M.2 2280', 'FastDisk'),
       (2, 1, 'Memória RAM 16GB (2x8) DDR4', 249.99, 'Memória', 'Kit 16GB 3200MHz', 'UDIMM', 'HyperMem'),
       (3, 2, 'Monitor 27\" 144Hz', 1299.00, 'Periférico', 'Monitor 27 polegadas 144Hz Full HD', 'VG27', 'ViewPro'),
       (4, 2, 'Teclado Mecânico RGB', 199.50, 'Periférico', 'Teclado mecânico com switches vermelhos', 'MK-Red',
        'KeyPro'),
       (5, 2, 'Placa de Vídeo GTX 1660 6GB', 1599.00, 'Placa de Vídeo', 'GPU 6GB GDDR5 para jogos', 'GTX1660',
        'Graphix');

INSERT IGNORE INTO imagem_produto (produto_id, arquivo, caminho)
VALUES (1, 'ssd_kingston.jpg', '../_arquivosmidia/produtos/'),
       (2, 'ram.jpg', '../_arquivosmidia/produtos/'),
       (3, 'monitor_tuf.jpg', '../_arquivosmidia/produtos/'),
       (4, 'teclado_hyperx.jpg', '../_arquivosmidia/produtos/'),
       (5, 'rx5600.jpg', '../_arquivosmidia/produtos/');
