create database technoup;
use technoup;

create table consumidor(
id_consumidor int not null auto_increment primary key,
email varchar(50),
senha varchar(50)
);

create table lojista(
id_lojista int not null auto_increment primary key,
email varchar(50),
senha varchar(50),
nome_loja varchar(50),
telefone int
);

create table produto(
id_produto int not null primary key,
nome varchar(50),
preco decimal(10,2),
tipo varchar(30)
);

create table catalogo(
id_catalogo int not null,
id_produto int not null,
id_lojista int not null,
preco decimal(10,2),
foreign key (id_produto) references produto(id_produto),
foreign key (id_lojista) references lojista(id_lojista)
);
