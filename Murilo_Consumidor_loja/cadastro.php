<?php
include("conexao.php");

$nome           = $_POST['nome'];
$cpf            = $_POST['cpf'];
$cep_consumidor = $_POST['cep'];
$estado         = $_POST['estado'];
$cidade         = $_POST['cidade'];
$bairro         = $_POST['bairro'];
$numero         = $_POST['numero'];
$genero         = $_POST['genero'];
$email          = $_POST['email'];
$senha          = $_POST['senha'];
$telefone       = $_POST['telefone'];

$sql = "INSERT INTO consumidor (nome, cpf, cep_consumidor, estado, cidade, bairro, numero, genero, email, senha, telefone, ativo)
        VALUES ('$nome', '$cpf', '$cep_consumidor', '$estado', '$cidade', '$bairro', '$numero', '$genero', '$email', '$senha', '$telefone', 1)";

if ($con->query($sql)) {
    echo "Consumidor cadastrado!";
} else {
    echo "Erro: " . $con->error;
}
?>