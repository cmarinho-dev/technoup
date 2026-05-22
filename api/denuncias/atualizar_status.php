<?php
// Atualiza o status de uma denuncia.

include_once '../conexao.php';
include_once '../funcoes.php';

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'administrador') {
    session_write_close();
    respostaJson('nok', 'Acesso restrito ao administrador.');
}
session_write_close();

$denunciaId = (int) ($_POST['id'] ?? 0);
$status = trim($_POST['status'] ?? '');
$statusPermitidos = ['pendente', 'em_analise', 'resolvida', 'recusada'];

if ($denunciaId <= 0) {
    respostaJson('nok', 'ID da denúncia é obrigatório.');
}

if (!in_array($status, $statusPermitidos, true)) {
    respostaJson('nok', 'Status inválido.');
}

$stmtBusca = $conexao->prepare("SELECT id FROM denuncia_conta WHERE id = ?");
$stmtBusca->bind_param('i', $denunciaId);
$stmtBusca->execute();
$resultadoBusca = $stmtBusca->get_result();
$stmtBusca->close();

if ($resultadoBusca->num_rows === 0) {
    respostaJson('nok', 'Denúncia não encontrada.');
}

$stmt = $conexao->prepare("UPDATE denuncia_conta SET status = ? WHERE id = ?");
$stmt->bind_param('si', $status, $denunciaId);
$stmt->execute();
$stmt->close();
$conexao->close();

respostaJson('ok', 'Status da denúncia atualizado.');
