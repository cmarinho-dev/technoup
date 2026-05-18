<?php
include_once '../conexao.php';

function respostaJson($status, $mensagem = '', $dados = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
    exit;
}

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

$status = trim($_GET['status'] ?? '');

if ($status !== '') {
    $stmt = $conexao->prepare("
        SELECT
            avaliacao_peca.*,
            conta.nome AS consumidor_nome,
            conta.email AS consumidor_email,
            chat_cotacao_usado.id AS chat_id
        FROM avaliacao_peca
        JOIN conta ON conta.id = avaliacao_peca.consumidor_id
        LEFT JOIN chat_cotacao_usado ON chat_cotacao_usado.avaliacao_id = avaliacao_peca.id
        WHERE avaliacao_peca.loja_id = ? AND avaliacao_peca.status = ?
        ORDER BY avaliacao_peca.criado_em DESC
    ");
    $stmt->bind_param('is', $lojaId, $status);
} else {
    $stmt = $conexao->prepare("
        SELECT
            avaliacao_peca.*,
            conta.nome AS consumidor_nome,
            conta.email AS consumidor_email,
            chat_cotacao_usado.id AS chat_id
        FROM avaliacao_peca
        JOIN conta ON conta.id = avaliacao_peca.consumidor_id
        LEFT JOIN chat_cotacao_usado ON chat_cotacao_usado.avaliacao_id = avaliacao_peca.id
        WHERE avaliacao_peca.loja_id = ?
        ORDER BY FIELD(avaliacao_peca.status, 'pendente', 'aceita', 'recusada'), avaliacao_peca.criado_em DESC
    ");
    $stmt->bind_param('i', $lojaId);
}

$stmt->execute();
$resultado = $stmt->get_result();

$avaliacoes = [];
while ($linha = $resultado->fetch_assoc()) {
    $avaliacoes[] = $linha;
}

$stmt->close();
$conexao->close();

respostaJson('ok', '', $avaliacoes);
