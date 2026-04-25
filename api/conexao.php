<?php

$server = 'localhost:3306';
$username = 'root';
$password = '';
$database = 'technoup';

// Conexão com o banco de dados technoup
$conexao = new mysqli($server, $username, $password, $database);
if ($conexao->connect_error) {
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode(['status' => 'nok', 'mensagem' => 'Erro de conexão com o banco.', 'data' => []]));
}
$conexao->set_charset('utf8mb4');
// Garante que a conexão use utf8mb4 para evitar problemas com caracteres acentuados
mysqli_query($conexao, "SET NAMES 'utf8mb4'");