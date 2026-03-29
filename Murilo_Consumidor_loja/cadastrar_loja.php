<?php
include("conexao.php");

$email = $_POST['email'];
$senha = $_POST['senha'];
$nome_loja = $_POST['nome_loja'];
$telefone = $_POST['telefone'];

$sql = "INSERT INTO lojista (email, senha, nome_loja, telefone, ativo)
        VALUES ('$email', '$senha', '$nome_loja', '$telefone', 1)";

if ($con->query($sql)) {
    echo "Loja cadastrada!";
} else {
    echo "Erro: " . $con->error;
}
?>