<?php
// Envia resposta JSON e encerra o script
function respostaJson($status, $mensagem = '', $dados = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
    exit;
}

// (Não usa receberJson; retorna estado da sessão)

session_start();

if (isset($_SESSION['usuario'])) {
    respostaJson('ok', '', [
        'usuario' => $_SESSION['usuario'],
        'loja'    => $_SESSION['loja'] ?? null
    ]);
} else {
    respostaJson('nok', 'Sessão não iniciada.');
}
