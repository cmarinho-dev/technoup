<?php
include_once '../conexao.php';

// Envia resposta JSON e encerra o script
function respostaJson($status, $mensagem = '', $dados = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
    exit;
}

$resultado = $conexao->query("SELECT * FROM loja ORDER BY nome_loja");
$lojas = [];
while ($loja = $resultado->fetch_assoc()) {
    $lojas[] = $loja;
}

$conexao->close();
respostaJson('ok', '', $lojas);
