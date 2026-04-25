<?php
// Envia resposta JSON e encerra o script
function respostaJson($status, $mensagem = '', $dados = []) {
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
	exit;
}

session_start();
session_unset();
session_destroy();

respostaJson('ok', 'Logout realizado com sucesso.');
