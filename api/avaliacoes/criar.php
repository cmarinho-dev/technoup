<?php
include_once '../conexao.php';

function respostaJson($status, $mensagem = '', $dados = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
    exit;
}

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'consumidor') {
    session_write_close();
    respostaJson('nok', 'Acesso restrito ao consumidor.');
}
$consumidorId = (int)$_SESSION['usuario']['id'];
session_write_close();

$lojaId = (int)($_POST['loja_id'] ?? 0);
$nome = trim($_POST['nome'] ?? '');
$categoria = trim($_POST['tipopeca'] ?? ($_POST['categoria'] ?? ''));
$estado = trim($_POST['estado'] ?? '');
$detalhes = trim($_POST['detalhes'] ?? '');

if ($lojaId <= 0 || $nome === '' || $categoria === '' || $estado === '') {
    respostaJson('nok', 'Loja, nome, tipo e estado da peça são obrigatórios.');
}

$stmtLoja = $conexao->prepare("
    SELECT loja.id
    FROM loja
    JOIN conta ON conta.id = loja.conta_id
    WHERE loja.id = ? AND conta.ativo = 1
    LIMIT 1
");
$stmtLoja->bind_param('i', $lojaId);
$stmtLoja->execute();
$loja = $stmtLoja->get_result()->fetch_assoc();
$stmtLoja->close();

if (!$loja) {
    respostaJson('nok', 'Loja não encontrada ou inativa.');
}

$stmt = $conexao->prepare("
    INSERT INTO avaliacao_peca (consumidor_id, loja_id, nome_peca, categoria, estado, detalhes)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param('iissss', $consumidorId, $lojaId, $nome, $categoria, $estado, $detalhes);
$stmt->execute();

if ($stmt->affected_rows <= 0) {
    $stmt->close();
    $conexao->close();
    respostaJson('nok', 'Não foi possível enviar a avaliação.');
}

$avaliacaoId = $stmt->insert_id;
$stmt->close();
$conexao->close();

respostaJson('ok', 'Avaliação enviada para a loja.', ['id' => $avaliacaoId]);
