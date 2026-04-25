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

// Busca o status atual
$stmt = $conexao->prepare("SELECT ativo FROM conta WHERE id = ?");
$stmt->bind_param('i', $contaId);
$stmt->execute();
$resultado = $stmt->get_result();
$stmt->close();

if ($resultado->num_rows === 0) {
    respostaJson('nok', 'Conta não encontrada.');
}

$conta      = $resultado->fetch_assoc();
$novoStatus = ((int)$conta['ativo'] === 1) ? 0 : 1;

// Aplica o novo status
$stmtUpdate = $conexao->prepare("UPDATE conta SET ativo = ? WHERE id = ?");
$stmtUpdate->bind_param('ii', $novoStatus, $contaId);
$stmtUpdate->execute();
$sucesso = $stmtUpdate->affected_rows > 0;
$stmtUpdate->close();
$conexao->close();

if ($sucesso) {
    respostaJson('ok', 'Status alterado com sucesso.', ['ativo' => $novoStatus]);
} else {
    respostaJson('nok', 'Não foi possível alterar o status.');
}
