<?php
include_once '../conexao.php';
include_once '../funcoes.php';

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'lojista') {
    session_write_close();
    respostaJson('nok', 'Acesso restrito ao lojista.');
}

$usuario = $_SESSION['usuario'];
$lojaId = (int)($_SESSION['loja']['id'] ?? 0);

if ($lojaId <= 0) {
    $contaId = (int)$usuario['id'];
    $stmtLoja = $conexao->prepare("SELECT id FROM loja WHERE conta_id = ? LIMIT 1");
    $stmtLoja->bind_param('i', $contaId);
    $stmtLoja->execute();
    $loja = $stmtLoja->get_result()->fetch_assoc();
    $stmtLoja->close();
    $lojaId = $loja ? (int)$loja['id'] : 0;
}
session_write_close();

if ($lojaId <= 0) {
    respostaJson('nok', 'Loja não encontrada na sessão.');
}

$avaliacaoId = (int)($_POST['avaliacao_id'] ?? 0);
$statusAvaliacao = trim($_POST['status_avaliacao'] ?? '');
$statusPermitidos = ['aguardando_envio', 'recebido', 'em_avaliacao', 'avaliado', 'proposta_enviada', 'finalizado'];

if ($avaliacaoId <= 0 || !in_array($statusAvaliacao, $statusPermitidos, true)) {
    respostaJson('nok', 'Informe uma avaliação e um status válido.');
}

$stmt = $conexao->prepare("
    UPDATE avaliacao_item
    SET status_avaliacao = ?
    WHERE id = ? AND loja_id = ? AND status = 'aceita'
");
$stmt->bind_param('sii', $statusAvaliacao, $avaliacaoId, $lojaId);
$stmt->execute();
$alterados = $stmt->affected_rows;
$stmt->close();
$conexao->close();

if ($alterados <= 0) {
    respostaJson('nok', 'Só é possível atualizar o andamento de avaliações aceitas.');
}

respostaJson('ok', 'Status da avaliação atualizado.', [
    'avaliacao_id' => $avaliacaoId,
    'status_avaliacao' => $statusAvaliacao
]);
