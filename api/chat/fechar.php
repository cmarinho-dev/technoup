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

$where = 'id = ?';
$params = [$chatId];
$types = 'i';

if ($usuario['tipo'] !== 'lojista') {
    respostaJson('nok', 'Apenas a loja pode fechar este chat.');
}

if ($lojaId <= 0) respostaJson('nok', 'Loja não encontrada na sessão.');
$where .= ' AND loja_id = ?';
$params[] = $lojaId;
$types .= 'i';

$stmt = $conexao->prepare("UPDATE chat_cotacao_item SET status = 'fechado', fechado_em = NOW() WHERE {$where}");
$stmt->bind_param($types, ...$params);
$stmt->execute();
$alterados = $stmt->affected_rows;
$stmt->close();
$conexao->close();

if ($alterados <= 0) respostaJson('nok', 'Não foi possível fechar este chat.');
respostaJson('ok', 'Chat fechado.');
