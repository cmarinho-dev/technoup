<?php
include("conexao.php");

$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];

$sql = "INSERT INTO consumidor (nome, email, senha, ativo)
        VALUES ('$nome', '$email', '$senha', 1)";

if ($con->query($sql)) {
    echo "Consumidor cadastrado!";
} else {
    echo "Erro: " . $con->error;
}
?>