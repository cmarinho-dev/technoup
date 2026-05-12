<?php
include_once '../conexao.php';

function respostaJson($status, $mensagem = '', $dados = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => $status, 'mensagem' => $mensagem, 'data' => $dados]);
    exit;
}

session_start();
if (!isset($_SESSION['usuario'])) {
    session_write_close();
    respostaJson('nok', 'Acesso restrito a usuários logados.');
}
$usuario = $_SESSION['usuario'];
$lojaId = (int)($_SESSION['loja']['id'] ?? 0);
session_write_close();

$chatId = (int)($_POST['chat_id'] ?? 0);
if ($chatId <= 0) respostaJson('nok', 'Chat inválido.');

$where = 'id = ?';
$params = [$chatId];
$types = 'i';

if ($usuario['tipo'] === 'consumidor') {
    $where .= ' AND consumidor_id = ?';
    $params[] = (int)$usuario['id'];
    $types .= 'i';
} elseif ($usuario['tipo'] === 'lojista') {
    if ($lojaId <= 0) respostaJson('nok', 'Loja não encontrada na sessão.');
    $where .= ' AND loja_id = ?';
    $params[] = $lojaId;
    $types .= 'i';
} elseif ($usuario['tipo'] !== 'administrador') {
    respostaJson('nok', 'Acesso restrito.');
}

$stmt = $conexao->prepare("UPDATE chat_cotacao_usado SET status = 'fechado', fechado_em = NOW() WHERE {$where}");
$stmt->bind_param($types, ...$params);
$stmt->execute();
$alterados = $stmt->affected_rows;
$stmt->close();
$conexao->close();

if ($alterados <= 0) respostaJson('nok', 'Não foi possível fechar este chat.');
respostaJson('ok', 'Chat fechado.');
