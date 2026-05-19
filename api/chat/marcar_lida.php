<?php
include_once '../conexao.php';
include_once '../funcoes.php';

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

if ($usuario['tipo'] === 'consumidor') {
    $stmt = $conexao->prepare("UPDATE chat_cotacao_item SET lido_consumidor_em = NOW() WHERE id = ? AND consumidor_id = ?");
    $usuarioId = (int)$usuario['id'];
    $stmt->bind_param('ii', $chatId, $usuarioId);
} elseif ($usuario['tipo'] === 'lojista') {
    if ($lojaId <= 0) respostaJson('nok', 'Loja não encontrada na sessão.');
    $stmt = $conexao->prepare("UPDATE chat_cotacao_item SET lido_lojista_em = NOW() WHERE id = ? AND loja_id = ?");
    $stmt->bind_param('ii', $chatId, $lojaId);
} else {
    $stmt = $conexao->prepare("UPDATE chat_cotacao_item SET lido_consumidor_em = NOW(), lido_lojista_em = NOW() WHERE id = ?");
    $stmt->bind_param('i', $chatId);
}

$stmt->execute();
$stmt->close();
$conexao->close();

respostaJson('ok', 'Chat marcado como lido.');
