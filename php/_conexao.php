<?php
// Variáveis de conexão com o Banco de Dados
$servidor = "localhost:3306";
$usuario  = "root";
$senha    = "Tamanco9!";
$nome_banco = "technoup";

$conexao = new mysqli($servidor, $usuario, $senha, $nome_banco);
if($conexao->connect_error){
    echo $conexao->connect_error;
}