<?php
include_once '../conexao.php';
include_once '../funcoes.php';

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'consumidor') {
    session_write_close();
    respostaJson('nok', 'Acesso restrito ao consumidor.');
}

$consumidorId = (int)$_SESSION['usuario']['id'];
session_write_close();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respostaJson('nok', 'Método inválido.');
}

$avaliacaoId = (int)($_POST['avaliacao_id'] ?? 0);
$nota = (int)($_POST['nota'] ?? 0);
$comentario = trim($_POST['comentario'] ?? '');

if ($avaliacaoId <= 0) {
    respostaJson('nok', 'Avaliação do item é obrigatória.');
}

if ($nota < 1 || $nota > 5) {
    respostaJson('nok', 'Informe uma nota entre 1 e 5.');
}

$stmt = $conexao->prepare("
    SELECT
        avaliacao_item.id,
        avaliacao_item.loja_id,
        avaliacao_item.status,
        avaliacao_item.status_avaliacao,
        chat_cotacao_item.status AS status_chat
    FROM avaliacao_item
    LEFT JOIN chat_cotacao_item ON chat_cotacao_item.avaliacao_id = avaliacao_item.id
    WHERE avaliacao_item.id = ? AND avaliacao_item.consumidor_id = ?
    LIMIT 1
");
$stmt->bind_param('ii', $avaliacaoId, $consumidorId);
$stmt->execute();
$avaliacao = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$avaliacao) {
    respostaJson('nok', 'Solicitação não encontrada para este consumidor.');
}

$atendimentoFinalizado = $avaliacao['status_avaliacao'] === 'finalizado' || $avaliacao['status_chat'] === 'fechado';
if ($avaliacao['status'] !== 'aceita' || !$atendimentoFinalizado) {
    respostaJson('nok', 'O atendimento precisa estar finalizado para receber avaliação.');
}

$lojaId = (int)$avaliacao['loja_id'];

$stmtExiste = $conexao->prepare("SELECT id FROM avaliacao_atendimento WHERE avaliacao_id = ? LIMIT 1");
$stmtExiste->bind_param('i', $avaliacaoId);
$stmtExiste->execute();
$jaAvaliada = $stmtExiste->get_result()->fetch_assoc();
$stmtExiste->close();

if ($jaAvaliada) {
    respostaJson('nok', 'Este atendimento já foi avaliado.');
}

$stmtInsert = $conexao->prepare("
    INSERT INTO avaliacao_atendimento (avaliacao_id, consumidor_id, loja_id, nota, comentario)
    VALUES (?, ?, ?, ?, ?)
");
$stmtInsert->bind_param('iiiis', $avaliacaoId, $consumidorId, $lojaId, $nota, $comentario);

if (!$stmtInsert->execute()) {
    $stmtInsert->close();
    respostaJson('nok', 'Não foi possível salvar a avaliação do atendimento.');
}

$stmtInsert->close();
$conexao->close();

respostaJson('ok', 'Avaliação enviada com sucesso.');
