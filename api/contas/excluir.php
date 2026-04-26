<?php
include_once '../conexao.php';

// Envia resposta JSON e encerra o script
function respostaJson($status, $mensagem = '', $dados = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
    exit;
}

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'administrador') {
    session_write_close();
    respostaJson('nok', 'Acesso restrito ao administrador.');
}
session_write_close();

$contaId = (int)($_POST['conta_id'] ?? 0);

if (empty($contaId)) {
    respostaJson('nok', 'ID da conta é obrigatório.');
}

// Caso seja deleção de contaLoja a exclusão em cascata remove automaticamente loja, produtos e imagens vinculados
$stmt = $conexao->prepare("DELETE FROM conta WHERE id = ?");
$stmt->bind_param('i', $contaId);
$stmt->execute();
$sucesso = $stmt->affected_rows > 0;
$stmt->close();
$conexao->close();

if ($sucesso) {
    respostaJson('ok', 'Conta e dados vinculados deletados com sucesso.');
} else {
    respostaJson('nok', 'Conta não encontrada.');
}
