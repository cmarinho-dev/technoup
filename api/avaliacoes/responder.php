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
$acao = trim($_POST['acao'] ?? '');

if ($avaliacaoId <= 0 || !in_array($acao, ['aceitar', 'recusar'], true)) {
    respostaJson('nok', 'Informe uma solicitação e uma ação válida.');
}

$stmt = $conexao->prepare("
    SELECT id, consumidor_id, loja_id, status
    FROM avaliacao_item
    WHERE id = ? AND loja_id = ?
    LIMIT 1
");
$stmt->bind_param('ii', $avaliacaoId, $lojaId);
$stmt->execute();
$avaliacao = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$avaliacao) {
    respostaJson('nok', 'Solicitação não encontrada para esta loja.');
}

if ($avaliacao['status'] !== 'pendente') {
    respostaJson('nok', 'Esta solicitação já foi respondida.');
}

$novoStatus = $acao === 'aceitar' ? 'aceita' : 'recusada';

$stmt = $conexao->prepare("
    UPDATE avaliacao_item
    SET status = ?, respondido_em = NOW()
    WHERE id = ? AND loja_id = ?
");
$stmt->bind_param('sii', $novoStatus, $avaliacaoId, $lojaId);
$stmt->execute();
$stmt->close();

$chatId = null;
if ($novoStatus === 'aceita') {
    $consumidorId = (int)$avaliacao['consumidor_id'];
    $stmt = $conexao->prepare("
        INSERT INTO chat_cotacao_item (consumidor_id, loja_id, avaliacao_id)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param('iii', $consumidorId, $lojaId, $avaliacaoId);
    $stmt->execute();
    $chatId = $stmt->insert_id;
    $stmt->close();
}

$conexao->close();

respostaJson('ok', $novoStatus === 'aceita' ? 'Solicitação aceita. Chat liberado.' : 'Solicitação recusada.', [
    'avaliacao_id' => $avaliacaoId,
    'status' => $novoStatus,
    'chat_id' => $chatId
]);
