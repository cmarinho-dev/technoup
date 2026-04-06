DROP DATABASE IF EXISTS technoup;

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
    criado_em   DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (loja_id) REFERENCES loja (id) ON DELETE CASCADE
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
        '2000', '11888888888', '../_arquivosmidia/lojas/kabum.jpg', 2),
       (3, 'ByteStorm', 'Rua XV de Novembro', '45612378901', '45612378000155', '80020000', 'PR', 'Curitiba', 'Centro',
        '320', '41977778888', '../_arquivosmidia/lojas/bytestorm.jpg', 4);

INSERT IGNORE INTO produto (id, loja_id, nome, preco, tipo, descricao, modelo, marca, desconto)
VALUES (1, 1, 'SSD NVMe 1TB', 499.90, 'Armazenamento', 'SSD NVMe M.2 com alta performance', 'M.2 2280', 'FastDisk', 0),
       (2, 1, 'Memória RAM 16GB (2x8) DDR4', 249.99, 'Memória', 'Kit 16GB 3200MHz', 'UDIMM', 'HyperMem', 0),
       (3, 2, 'Monitor 27" 144Hz', 1299.00, 'Periférico', 'Monitor 27 polegadas 144Hz Full HD', 'VG27', 'ViewPro', 0),
       (4, 2, 'Teclado Mecânico RGB', 199.50, 'Periférico', 'Teclado mecânico com switches vermelhos', 'MK-Red',
        'KeyPro', 0),
       (5, 2, 'Placa de Vídeo GTX 1660 6GB', 1599.00, 'Placa de Vídeo', 'GPU 6GB GDDR5 para jogos', 'GTX1660',
        'Graphix', 0),
       (6, 1, 'Fonte 650W 80 Plus Bronze', 349.90, 'Hardware', 'Fonte ATX com cabo mallado e PFC ativo', 'PS650B',
        'VoltX', 10),
       (7, 1, 'Gabinete Mid Tower RGB', 279.90, 'Hardware', 'Gabinete com lateral em vidro e 3 fans inclusas',
        'Glass X3', 'DarkCase', 15),
       (8, 1, 'SSD SATA 480GB', 189.90, 'Armazenamento', 'SSD SATA 2.5 para upgrade rapido', 'S480', 'FastDisk', 0),
       (9, 2, 'Mouse Gamer RGB 7200 DPI', 129.90, 'Periférico', 'Mouse com 6 botoes programaveis', 'GM7200',
        'KeyPro', 5),
       (10, 2, 'Headset USB Surround', 219.90, 'Periférico',
        'Headset com microfone destacavel e audio virtual 7.1', 'HS700', 'SoundMax', 12),
       (11, 2, 'Webcam Full HD', 179.90, 'Periférico', 'Webcam 1080p para stream e reunioes', 'Cam1080', 'ViewPro', 0),
       (12, 3, 'Notebook Ryzen 7 16GB 512GB SSD', 3899.90, 'Notebook',
        'Notebook para produtividade e multitarefa', 'NotePro 15', 'ByteTech', 8),
       (13, 3, 'Cadeira Gamer Ajustavel', 899.90, 'Cadeira',
        'Cadeira com apoio lombar e reclinacao de 180 graus', 'SeatPro X', 'ComfortPlay', 20),
       (14, 3, 'Monitor Ultrawide 29" 100Hz', 1499.90, 'Monitor',
        'Monitor ultrawide IPS para trabalho e jogos', 'UW29', 'ViewPro', 7),
       (15, 3, 'Kit Teclado e Mouse Sem Fio', 159.90, 'Periférico',
        'Combo sem fio para escritorio e uso diario', 'KM200', 'OfficeGo', 0),
       (16, 3, 'Placa Mae B550 AM4', 799.90, 'Hardware',
        'Placa mae com suporte a Ryzen serie 5000', 'B550M Pro', 'BoardMax', 10);

INSERT IGNORE INTO imagem_produto (produto_id, arquivo, caminho)
VALUES (1, 'ssd_kingston.jpg', '../_arquivosmidia/produtos/'),
       (2, 'ram.jpg', '../_arquivosmidia/produtos/'),
       (3, 'monitor_tuf.jpg', '../_arquivosmidia/produtos/'),
       (4, 'teclado_hyperx.jpg', '../_arquivosmidia/produtos/'),
       (5, 'rx5600.jpg', '../_arquivosmidia/produtos/'),
       (6, 'fonte_650w.jpg', '../_arquivosmidia/produtos/'),
       (7, 'gabinete_rgb.jpg', '../_arquivosmidia/produtos/'),
       (8, 'ssd_sata_480.jpg', '../_arquivosmidia/produtos/'),
       (9, 'mouse_gamer_rgb.jpg', '../_arquivosmidia/produtos/'),
       (10, 'headset_usb.jpg', '../_arquivosmidia/produtos/'),
       (11, 'webcam_fullhd.jpg', '../_arquivosmidia/produtos/'),
       (12, 'notebook_ryzen7.jpg', '../_arquivosmidia/produtos/'),
       (13, 'cadeira_gamer.jpg', '../_arquivosmidia/produtos/'),
       (14, 'monitor_ultrawide.jpg', '../_arquivosmidia/produtos/'),
       (15, 'kit_teclado_mouse.jpg', '../_arquivosmidia/produtos/'),
       (16, 'placa_mae_b550.jpg', '../_arquivosmidia/produtos/');
