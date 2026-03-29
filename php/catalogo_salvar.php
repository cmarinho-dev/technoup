<?php
include '_conexao.php';

$nome = $_POST['nome'];
$preco = $_POST['preco'];
$tipo = $_POST['tipo'];

$conexao->query("INSERT INTO produto (nome, preco, tipo) 
VALUES ('$nome', '$preco', '$tipo')");

header("Location: ../ui/pages/catalogo/");
exit;