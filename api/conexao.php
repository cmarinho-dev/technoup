<?php
// Conexão com o banco de dados technoup
$conexao = new mysqli('localhost:3306', 'root', '', 'technoup');
if ($conexao->connect_error) {
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode(['status' => 'nok', 'mensagem' => 'Erro de conexão com o banco.', 'data' => []]));
}
$conexao->set_charset('utf8');

// Envia resposta JSON e encerra o script
function respostaJson($status, $mensagem = '', $dados = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
    exit;
}

// Lê e decodifica o corpo JSON da requisição
function receberJson() {
    $corpo = file_get_contents('php://input');
    $dados = json_decode($corpo, true);
    if (!is_array($dados)) {
        respostaJson('nok', 'Dados inválidos na requisição.');
    }
    return $dados;
}
