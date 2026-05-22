<?php
include_once 'validacoes.php';
include_once 'midias.php';

// Envia resposta JSON e encerra o script
function respostaJson($status, $mensagem = '', $dados = [])
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
    exit;
}