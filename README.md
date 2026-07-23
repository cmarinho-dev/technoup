<div align="center">

# TechnoUP

[Instalação](#instalação) • [Funcionalidades](#funcionalidades) • [Modelo de Dados](#modelo-de-dados) • [Links Úteis](#links-úteis)

![JavaScript](https://img.shields.io/badge/JavaScript-42.4%25-F7DF1E?logo=javascript&logoColor=black)
![PHP](https://img.shields.io/badge/PHP-Backend-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-UI-38BDF8?logo=tailwindcss&logoColor=white)
![Status](https://img.shields.io/badge/status-acad%C3%AAmico-yellow)

</div>

---

### Sumário
- [Introdução](#introdução)
- [Funcionalidades](#funcionalidades)
- [Modelo de Dados](#modelo-de-dados)
- [Pré-requisitos](#pré-requisitos)
- [Instalação](#instalação)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Links Úteis](#links-úteis)

# Introdução

**TechnoUP** é um projeto fullstack desenvolvido e apresentado na disciplina de **Experiência Criativa**, do curso de Bacharelado em Engenharia de Software. A proposta é oferecer uma plataforma onde lojas de eletrônicos possam informatizar seus produtos em um catálogo digital, centralizando cadastro de contas, lojas e produtos.

# Funcionalidades

- Cadastro de contas de usuário, com diferentes tipos de acesso;
- Vínculo entre uma conta e uma loja (CNPJ, cidade e estado);
- Cadastro de produtos por loja, com preço, desconto e preço final calculado;
- Upload/associação de imagens aos produtos;
- Interface web estilizada com **TailwindCSS**;
- Backend em **PHP**, integrado a um banco **MySQL**.

# Modelo de Dados

O projeto gira em torno de quatro entidades principais: uma conta pode estar associada a uma loja, que por sua vez vende diversos produtos, cada um podendo ter uma imagem associada.

```
erDiagram
    CONTA ||--o| LOJA : "Conta pode ter loja"
    LOJA ||--o{ PRODUTO : "Loja vende produtos"
    PRODUTO ||--o| IMAGEM_PRODUTO : "Produto possui imagem"

    CONTA {
        INT id PK
        VARCHAR nome
        VARCHAR email
        VARCHAR senha
        ENUM tipo
        TINYINT ativo
        DATETIME criado_em
    }

    LOJA {
        INT id PK
        INT conta_id FK
        VARCHAR nome_loja
        VARCHAR cnpj
        VARCHAR cidade
        VARCHAR estado
    }

    PRODUTO {
        INT id PK
        INT loja_id FK
        VARCHAR nome
        DECIMAL preco
        INT desconto
        DECIMAL preco_final
    }

    IMAGEM_PRODUTO {
        INT id PK
        INT produto_id FK
        VARCHAR arquivo
        VARCHAR caminho
    }
```

O script de criação do banco está disponível em [`script.sql`](script.sql), na raiz do repositório.

# Pré-requisitos

- **Servidor Apache** e **MySQL** — a forma mais simples é usar o [XAMPP](https://www.apachefriends.org/pt_br/index.html);
- Um cliente para rodar scripts SQL (o próprio phpMyAdmin do XAMPP resolve).

# Instalação

1. Instale o XAMPP e inicie os serviços **Apache** e **MySQL**.
2. Acesse `http://localhost/phpmyadmin`, crie o banco do projeto e execute o script disponível em [`script.sql`](script.sql) na aba `SQL`.
3. Copie todos os arquivos do projeto para o diretório `htdocs` do XAMPP (geralmente `C:\xampp\htdocs\technoup`).
4. Acesse o projeto pelo navegador em [`http://localhost/technoup`](http://localhost/technoup).

# Estrutura do Projeto

```
technoup/
├── api/          # Backend (PHP) — regras de negócio e acesso ao banco
├── frontend/      # Interface web (HTML, CSS/TailwindCSS, JavaScript)
├── imagens/       # Imagens estáticas do projeto/produtos
├── videos/        # Vídeos de apresentação/demonstração
└── script.sql     # Script de criação do banco de dados
```

# Links Úteis

- [Documento de Especificação/Escopo](https://docs.google.com/document/d/1fP2VfiEM8JeYFwJy-tuD6_hT2eX5zqZnMf4FrSsntLs/edit?usp=sharing)
- [Quadro no Trello](https://trello.com/invite/b/69b7ebbefbed913f2b15fe93/ATTIac3d810cceecb08c0f044a8607dbd1328488E02B/projetotecnoup)

---

<div align="center">

Projeto acadêmico desenvolvido para a disciplina de Experiência Criativa — Engenharia de Software.

</div>
