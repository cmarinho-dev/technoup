<?php
include '_conexao.php';

$nome = $_POST['nome'];
$preco = $_POST['preco'];
$tipo = $_POST['tipo'];
$descricao = $_POST['descricao'];
$modelo = $_POST['modelo'];
$marca = $_POST['marca'];
$capacidade = $_POST['capacidade'];
$tipodamemoria = $_POST['tipodamemoria'];
$conector = $_POST['conector'];

$conexao->query("INSERT INTO produto (nome, preco, tipo, descricao, modelo, marca, capacidade, tipodamemoria, conector) 
VALUES ('$nome', '$preco', '$tipo', '$descricao', '$modelo', '$marca', '$capacidade', '$tipodamemoria', '$conector')");

header("Location: ../ui/pages/catalogo/");
exit;